<?php

/*
 * You can place your custom package configuration in here.
 */
return [


    /**
     * the route used to post payment data
     */
    'routes' => [

        'submit-payment' => '/api/payment',

        'form-route' => '/api/form',
    ],

    'payfort' => [

        'callback_urls' => [
            'error-page' => '/api/error',
            'success-page' => '/api/success',
        ],
        'sandboxMode' => env('PAYFORT_SAND_BOX_MODE', true),

        /**
         * language used to specify the response language returned from payfort
         */
        'language' => env('LANGUAGE', 'en'),

        /**
         * your Merchant Identifier account (mid)
         */
        'merchantIdentifier' => env('MERCHANT_IDENTIFIER', ''),

        /**
         * your access code
         */
        'accessCode' => env('ACCESS_CODE', ''),

        /**
         * SHA Request passphrase
         */
        'SHARequestPhrase' => env('SHA_REQUEST_PASSPHRASE', ''),

        /**
         * SHA Response passphrase
         */
        'SHAResponsePhrase' => env('SHA_RESPONSE_PASSPHRASE', ''),

        /**
         * SHA Type (Hash Algorith)
         * expected Values ("sha1", "sha256", "sha512")
         */
        'SHAType' => env('SHA_TYPE', 'sha256'),

        /**
         * command
         * expected Values ("AUTHORIZATION", "PURCHASE")
         */
        'command' => env('COMMAND', 'AUTHORIZATION'),

        /**
         * order currency
         */
        'currency'   => env('CURRENCY', 'USD'),
    ],

    /**
     * 
     *  payfort Apple Pay configuration
     * 
     */

    'payfort_apple_pay' => [

        'sandboxMode'           => true,
        'language'              => 'ar',
        'merchantIdentifier'    => '',
        'accessCode'            => '',
        'SHARequestPhrase'      => '',
        'SHAResponsePhrase'     => '',
        'SHAType'     => 'sha256',
        'command' => 'PURCHASE',
        'currency' => 'SAR',

    ]
];
