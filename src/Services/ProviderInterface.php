<?php

namespace Devinweb\Payment\Services;

use Illuminate\Http\Request;

interface ProviderInterface
{

    /**
     * 
     */
    public function __construct(Request $request, array $config, $merchant_reference);
    /**
     * 
     */
    public function pay();

    /**
     * 
     */
    public function responseCallback();

    /**
     * 
     */
    public function webHook();

    /**
     * 
     */
    public function getMerchantReference();
}
