<?php

namespace Devinweb\Payment\Services;

use Illuminate\Http\Request;


abstract class AbstractProvider
{
    /**
     * The HTTP request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;


    /**
     * The Merchant Reference.
     *
     * @var string
     */
    protected $merchantReference;


    /**
     * The Config data for each Provider.
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new provider instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $merchantReference
     * @return void
     */
    public function __construct(Request $request, array $config, $merchantReference)
    {

        $this->request = $request;
        $this->config = $config;
        $this->merchantReference = $merchantReference;
    }
}
