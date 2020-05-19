<?php

namespace Devinweb\Payment\Services\Payfort;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

abstract class AbstractPayfort
{
    /**
     * 
     */
    protected $request_data;

    /**
     * 
     */
    protected $user_params;

    /**
     * 
     */
    protected $config;


    /**
     * 
     */
    protected $useReactNative;



    /**
     * 
     */
    public function __construct(Request $request, array $config)
    {

        $this->request_data = $request;
        $this->config = $config;
        $this->user_params = Cache::get($request->get('merchant_reference'));
        $this->useReactNative = Cache::get('reactNative_' . $this->request_data->get('merchant_reference'));
    }
}
