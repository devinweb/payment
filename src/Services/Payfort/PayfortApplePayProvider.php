<?php

namespace Devinweb\Payment\Services\Payfort;

use Devinweb\Payment\Services\AbstractProvider;
use Devinweb\Payment\Services\ProviderInterface;

class PayfortApplePayProvider extends AbstractProvider implements ProviderInterface
{


    /**
     * 
     */
    public function pay()
    {
        return (new PayfortApplePay($this->request, $this->config, $this->merchantReference))->processRequest();
    }

    /**
     * 
     */
    public function responseCallback()
    {
    }

    /**
     * 
     */
    public function webHook()
    {
    }

    /**
     * 
     */
    public function getMerchantReference()
    {
    }
}
