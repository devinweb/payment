<?php

namespace Devinweb\Payment\Services\Payfort;

use Devinweb\Payment\Services\Payfort\PayfortProcessResponse;
use Devinweb\Payment\Services\Payfort\PayfortTokenization;
use Devinweb\Payment\Services\Payfort\PayfortResponseRequest;
use Devinweb\Payment\Traits\Payfort\PayfortServices;
use Devinweb\Payment\Services\AbstractProvider;
use Devinweb\Payment\Services\ProviderInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Devinweb\Payment\Events\FailedTransaction;
use Devinweb\Payment\Events\SuccessTransaction;
use Illuminate\Support\Facades\Cache;

class PayfortProvider  extends AbstractProvider implements ProviderInterface
{
    use PayfortServices {
        calculateSignature as protected calculate_signature;
    }




    /**
     * 
     * @return $this
     */

    public function viaReactNative()
    {
        Cache::forever('reactNative_' . $this->merchantReference, true);
        return $this;
    }


    /**
     * 
     */
    public function pay()
    {

        return (new PayfortTokenization($this->request, $this->config, $this->merchantReference))->processRequest();
    }


    /**
     * 
     */
    public function responseCallback()
    {
        return (new PayfortResponseRequest($this->request, $this->config))->processMerchantPageResponse();
    }

    /**
     * 
     */
    public function processResponseCallback()
    {
        return (new PayfortProcessResponse($this->request, $this->config))->processResponse();
    }

    /**
     * 
     */
    public function getMerchantReference()
    {
        return $this->request->get('merchant_reference');
    }

    /**
     * 
     */
    public function webHook()
    {

        if ($this->request->get('signature') === $this->calculateSignature($this->request->toArray(), 'response')) {
            if (substr($this->request->get('response_code'), 2) == '000') {

                return event(new SuccessTransaction($this->request->toArray()));
            }
            return event(new FailedTransaction($this->request->toArray()));
        }

        throw new InvalidArgumentException('Signature Mismatch.');
    }


    /**
     * @param array $params
     * @param string $type
     * @return string
     */
    public function calculateSignature(array $params, $type)
    {
        $validator = Validator::make(['type' => $type], ['type' => 'required|in:request,response']);
        if ($validator->fails()) {
            throw new ValidationException('The given data was invalid.');
        }

        if (Arr::has($params, 'signature')) {
            $params = Arr::except($params, ['signature']);
        }

        return $this->calculate_signature($params, $type);
    }
}
