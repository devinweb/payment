<?php

namespace Devinweb\Payment\Services;

use Devinweb\Payment\Services\Payfort\AbstractPayfort;
use Illuminate\Http\Request;
use Devinweb\Payment\Traits\Payfort\PayfortServices;
use Devinweb\Payment\Traits\Payfort\PayfortApi;
use Illuminate\Support\Arr;

class PayfortProcessResponse extends AbstractPayfort
{
    use PayfortServices, PayfortApi;


    public function processResponse()
    {
        $fortParams = array_merge($_GET, $_POST);

        $debugMsg = "Fort Redirect Response Parameters \n" . print_r($fortParams, 1);

        $reason        = '';
        $response_code = '';
        $success = true;
        if (empty($fortParams)) {
            $success = false;
            $reason = "Invalid Response Parameters";
            $debugMsg = $reason;
        } else {
            //validate payfort response
            $params        = $fortParams;
            $responseSignature     = $fortParams['signature'];
            $merchantReference = $params['merchant_reference'];
            unset($params['r']);
            unset($params['signature']);
            unset($params['integration_type']);
            $calculatedSignature = $this->calculateSignature($params, 'response');
            $success       = true;
            $reason        = '';

            if ($responseSignature != $calculatedSignature) {
                $success = false;
                $reason  = 'Invalid signature.';
                $debugMsg = sprintf('Invalid Signature. Calculated Signature: %1s, Response Signature: %2s', $responseSignature, $calculatedSignature);
            } else {
                $response_code    = $params['response_code'];
                $response_message = $params['response_message'];
                $status           = $params['status'];
                if (substr($response_code, 2) != '000') {
                    $success = false;
                    $reason  = $response_message;
                    $debugMsg = $reason;
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
            return $this->postMessageReactNative(collect($params)->toJson());
        }
        return redirect($return_url);
    }
}
