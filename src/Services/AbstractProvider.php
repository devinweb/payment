<?php

namespace Devinweb\Payment\Services;

use Illuminate\Http\Request;
use InvalidArgumentException;


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

        // if (is_null($request->get('amount'))) {
        //     dd(is_null($request->get('amount')));
        //     throw new InvalidArgumentException("the amount is required !");
        // }

        // if (is_null($request->get('email'))) {
        //     throw new InvalidArgumentException("the customer email is required !");
        // }

        // if (is_null($request->get('hold_name'))) {
        //     throw new InvalidArgumentException("the customer hold_name is required !");
        // }
    }
}
