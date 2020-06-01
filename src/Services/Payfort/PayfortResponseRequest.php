<?php

namespace Devinweb\Payment\Services\Payfort;


use Devinweb\Payment\Events\TransactionConfirmed;
use Devinweb\Payment\Services\Payfort\AbstractPayfort;
use Illuminate\Http\Request;
use Devinweb\Payment\Traits\Payfort\PayfortServices;
use Devinweb\Payment\Traits\Payfort\PayfortApi;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PayfortResponseRequest extends AbstractPayfort
{

    use PayfortServices, PayfortApi;



    /**
     * 
     */
    public function processMerchantPageResponse()
    {

        // $fortParams = array_merge($_GET, $_POST);
        $fortParams = $this->request_data->toArray();
        $debugMsg = "Fort Merchant Page Response Parameters \n" . print_r($fortParams, 1);
        // $this->log($debugMsg);
        Log::info(['step_2' => $fortParams]);
        $reason = '';
        $response_code = '';
        $success = true;
        if (empty($fortParams)) {
            $success = false;
            $reason = "Invalid Response Parameters";
            $debugMsg = $reason;
            // $this->log($debugMsg);
        } else {
            //validate payfort response
            $params        = $fortParams;
            $responseSignature     = $fortParams['signature'];
            unset($params['r']);
            unset($params['signature']);
            unset($params['integration_type']);
            unset($params['3ds']);
            $merchantReference = $params['merchant_reference'];
            $calculatedSignature = $this->calculateSignature($params, 'response');

            $success       = true;
            $reason        = '';

            if ($responseSignature != $calculatedSignature) {
                $success = false;
                $reason  = 'Invalid signature.';
                $debugMsg = sprintf('Invalid Signature. Calculated Signature: %1s, Response Signature: %2s', $responseSignature, $calculatedSignature);
                Log::info("1__$debugMsg");
            } else {
                $response_code    = $params['response_code'];
                $response_message = $params['response_message'];
                $status           = $params['status'];
                if (substr($response_code, 2) != '000') {
                    $success = false;
                    $reason  = $response_message;
                    $debugMsg = $reason;
                    // $this->log($debugMsg);
                    Log::info("2__$debugMsg");
                } else {
                    $success         = true;
                    $host2HostParams = $this->merchantPageNotifyFort($fortParams);
                    $debugMsg = "Fort Merchant Page Host2Hots Response Parameters \n" . print_r($fortParams, 1);
                    // $this->log($debugMsg);
                    Log::info("3__$debugMsg");
                    if (!$host2HostParams) {
                        $success = false;
                        $reason  = 'Invalid response parameters.';
                        $debugMsg = $reason;
                        Log::info("4__$debugMsg");
                    } else {
                        $params    = $host2HostParams;
                        Log::info(["5__" => $params]);
                        $responseSignature = $host2HostParams['signature'];
                        $merchantReference = $params['merchant_reference'];
                        unset($params['r']);
                        unset($params['signature']);
                        unset($params['integration_type']);
                        $calculatedSignature = $this->calculateSignature($params, 'response');
                        if ($responseSignature != $calculatedSignature) {
                            Log::info(['responseSignature___payfortResponseRequest' => $responseSignature]);
                            Log::info(['calculatedSignature___payfortResponseRequest' => $calculatedSignature]);
                            $success = false;
                            $reason  = 'Invalid signature.';
                            $debugMsg = sprintf('Invalid Signature. Calculated Signature: %1s, Response Signature: %2s', $responseSignature, $calculatedSignature);
                            // $this->log($debugMsg);
                        } else {
                            $response_code = $params['response_code'];
                            if ($response_code == '20064' && isset($params['3ds_url'])) {
                                $success = true;
                                $debugMsg = 'Redirect to 3DS URL : ' . $params['3ds_url'];
                                $this->log($debugMsg);
                                echo "<html><body onLoad=\"javascript: window.top.location.href='" . $params['3ds_url'] . "'\"></body></html>";
                                exit;
                                //header('location:'.$params['3ds_url']);
                            } else {
                                if (substr($response_code, 2) != '000') {
                                    $success = false;
                                    $reason  = $host2HostParams['response_message'];
                                    $debugMsg = $reason;
                                    $this->log($debugMsg);
                                }
                            }
                        }
                    }
                }
            }
        }
        if (!$success) {
            // $p = $params;
            $params['error_msg'] = $reason;
            $return_url = url(Arr::get($this->config, 'callback_urls.error-page')) . '?' . http_build_query($params);
        } else {
            $return_url = url(Arr::get($this->config, 'callback_urls.success-page')) . '?' . http_build_query($params);
        }

        if ($this->useReactNative) {
            Log::info(['callback_from_here', $params]);
            return $this->postMessageReactNative(collect($params)->toJson());
        }
        return redirect($return_url);
    }

    /**
     * 
     */
    public function merchantPageNotifyFort($fortParams)
    {

        $postData      = array(
            'merchant_reference'  => $fortParams['merchant_reference'],
            'access_code'         => Arr::get($this->config, 'accessCode'),
            'command'             => Arr::get($this->config, 'command'),
            'merchant_identifier' => Arr::get($this->config, 'merchantIdentifier'),
            'customer_ip'         => $_SERVER['REMOTE_ADDR'],
            'amount'              => $this->convertFortAmount($this->user_params['amount'], Arr::get($this->config, 'currency')),
            'currency'            => strtoupper(Arr::get($this->config, 'currency')),
            'customer_email'      => $this->user_params['email'],
            'customer_name'       => $this->user_params['hold_name'],
            'token_name'          => $fortParams['token_name'],
            'language'            => Arr::get($this->config, 'language'),
            'return_url'          => url('/api/payfort/process-response'),
        );


        if (isset($fortParams['3ds']) && $fortParams['3ds'] == 'no') {
            $postData['check_3ds'] = 'NO';
        }



        //calculate request signature
        $signature             = $this->calculateSignature($postData, 'request');
        $postData['signature'] = $signature;

        $debugMsg = "Fort Host2Host Request Parameters \n" . print_r($postData, 1);
        $this->log($debugMsg);

        if (Arr::get($this->config, 'sandboxMode')) {
            $gatewayUrl = 'https://sbpaymentservices.payfort.com/FortAPI/paymentApi';
        } else {
            $gatewayUrl = 'https://paymentservices.payfort.com/FortAPI/paymentApi';
        }

        $array_result = $this->callApi($postData, $gatewayUrl);

        $debugMsg = "Fort Host2Host Response Parameters \n" . print_r($array_result, 1);
        $this->log($debugMsg);
        return  json_decode($array_result, true);
    }
}
