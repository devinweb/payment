# Very short description of the package

Payment Package provide a simple way to handle **payment gateway** in MENA.

## Installation

This package requires Laravel 5.4 or higher.
if your laravel is > 5.4 you can skip the two steps (2, 3) below (Package Auto Discovery 5.5+).

1.  You can install the package via composer:

```bash
composer require devinweb/payment
```

2. Open your `config/app.php` and add the following to the providers array

```php
Devinweb\Payment\PaymentServiceProvider::class,
```

3. In the same `config/app.php` and add the following to the aliases array:

```php
'Payment'   => Devinweb\Payment\Facades\Payment::class,
```

4. Run the command below to publish the package config file `config/payfort-payment.php`:

```bash
php artisan vendor:publish --provider="Devinweb\Payment\PaymentServiceProvider" --tag="config"
```

-   [Payfort](#Payfort)

    -   [Configuration (Payfort)](<#Configuration-(Payfort)>)
    -   [Routing (Payfort)](<#Routing-(Payfort)>)
    -   [Required Parameters (Payfort)](<#Required-Parameters-(Payfort)>)

-   [Payfort Apple Pay](#Payfort-apple-pay)

    -   [Configuration (Payfort Apple Pay)](<#Configuration-(Payfort-Apple-Pay)>)
    -   [Usage (Payfort Apple Pay)](<#Usage-(Payfort-Apple-Pay)>)
    -   [Required Parameters (Payfort Apple Pay)](<#Required-Parameters-(Payfort-Apple-Pay)>)

-   [Payfort Events](#Payfort-events)

-   [Exemple](#exemple)

# Payfort

To pay via payfort it's simple, but before process the payment it's requiered to setup your `FrontEnd`, you can check the [`payment-boilerplate`](https://github.com/devinweb/payment-boilerplate) Repository.

## Configuration (Payfort)

Now you can add your payfort credentiels to `app/config/payments/php`

```php

<?php

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
    ]
```

## Routing (Payfort)

Next to use Payfort, you need three Routes:

1. Used to submit data from the front end to the backend.
2. The second if the payment is successfuly.
3. For redirecting if payfort response with error.

```php

<?php

  // App\Config\payments.php

  // you can configure you callback routes here

  'payfort' => [

        'callback_urls' => [
            'error-page' => '', // redirection to error page
            'success-page' => '', // redirection to success page
        ],
        // ...
  ]

```

you can access to Payment using `Payment` facade.

```php

<?php

  use Illuminate\Http\Request;

  Route::post('/payment', function (Request $request) {

    // ...

    $merchant_reference = rand(0, getrandmax());

    return Payment::use('payfort', $merchant_reference)->pay();

  });
```

## Required Parameters (Payfort)

Each request should be contains `amount`, `email`, `hold_name`

```php

<?php

  $request->add([
    'amount' => '',
    'email' => '',
    'hold_name' => ''
  ])

```

# Payfort Apple Pay

You can use this package to handle the apple pay transactions via Payfort.

## Configuration (Payfort Apple Pay)

you should add payfort apple pay credentiels to the `app/confing/payments/php`

```php

<?php

'payfort_apple_pay' => [

        'sandboxMode'           => true,

        'language'              => 'ar',

        'merchantIdentifier'    => '',

        'accessCode'            => '',

        'SHARequestPhrase'      => '',

        'SHAResponsePhrase'     => '',

        'SHAType'               => 'sha256',

        'command'               => 'PURCHASE',

        'currency'              => 'SAR',

    ]

```

## Usage (Payfort Apple Pay)

To pay via Payfort apple pay is the the same as before you can use.

```php

 return Payment::use('payfort_apple_pay', $merchant_reference)->pay();

```

## Required Parameters (Payfort Apple Pay)

The parameters required to be attached in your request payload to use Apple Pay is:

```json
{
    "apple_data": "",
    "apple_header": {
        "apple_ephemeralPublicKey": "",
        "apple_publicKeyHash": "",
        "apple_transactionId": "-"
    },
    "apple_paymentMethod": {
        "apple_displayName": "",
        "apple_network": "",
        "apple_type": ""
    },
    "apple_signature": "",
    "apple_version": "EC_v1",
    "digital_wallet": "APPLE_PAY",

    //package requirements
    "amount": 240,

    // optional data if you need them
    "email": "",
    "name": "",
    "phone": ""
}
```

# Pay With ReactNative

if you want to process the payment via webView in your react native mobile app

```js
function onNavigationStateChange() {
    // this method will be invoked each time the url changed
}

function onMessage() {
    // here you can handle the final result
}

return (
    <WebView
        source={{ html: html() }}
        onNavigationStateChange={onNavigationStateChange}
        javaScriptEnabled={true}
        domStorageEnabled={true}
        onMessage={onMessage}
        startInLoadingState
    />
);
```

In your php controller or route you can use

```php
<?php

  return Payment::use('payfort')->viaReactNative()->pay();
```

## Payfort Events

As we know payfort can notify the merchant, for all events you subscribed for on an transaction. we need the first to add your callback into payfort dashboard, then you can implement this callback in your application

```php

<?php

  use Illuminate\Http\Request;

  Route::match(['get', 'post'], '/payfort-callback', function(Request $request) {

    return Payment::use('payfort')->webHook();

  });

```

This webHook can invoks two events build in. There are two events available for you to listen for.

| Event                                        | Fired                                          | Parameter                                   |
| -------------------------------------------- | ---------------------------------------------- | ------------------------------------------- |
| `Devinweb\Payment\Events\SuccessTransaction` | when payfort response with the successful data | array [success response](#success-response) |
| `Devinweb\Payment\Events\FailTransaction`    | when payfort response with the Fail data       | array [fail_response](#fail-response)       |

## exemple

check this repos [`payment-boilerplate`](https://github.com/devinweb/payment-boilerplate).

### Success response

```php
[
  "response_code" => "18000",
  "card_number" => "400555******0001",
  "card_holder_name" => "CUSTOMER_HOLDER_NAME",
  "signature" => "d641d71c13da959cba92371d70c686b602e2b62796dfca5286c760c6b5d9e3b1",
  "merchant_identifier" => "YOUR_MERCHANT_IDENTIFIER",
  "expiry_date" => "2105",
  "access_code" => "YOUR_ACCESS_CODE",
  "language" => "ar",
  "service_command" => "TOKENIZATION",
  "response_message" => "عملية ناجحة",
  "merchant_reference" => "278245857",
  "token_name" => "dced12c0eeeb444185dcc450b917d987",
  "return_url" => "YOUR_RETURN_URL"
  "card_bin" => "400555"
  "status" => "18"
]

```

### Fail response

```php
[
"response_code" => "00016",
"card_number" => "400550******0001",
"card_holder_name" => "CUSTOMER_HOLDER_NAME",
"signature" => "signature_value",
"merchant_identifier" => "YOUR_MERCHANT_IDENTIFIER",
"expiry_date" => "2105",
"access_code" => "YOUR_ACCESS_CODE",
"language" => "ar",
"service_command" => "TOKENIZATION",
"response_message" => "رقم البطاقة غير صحيح",
"merchant_reference" => "158151963",
"return_url" => "YOUR_RETURN_URL",
"status" => "00",
"error_msg" => "رقم البطاقة غير صحيح",
]
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

-   [darbaoui imad](https://github.com/darbaoui)
-   [All Contributors](../../contributors)

## About Devinweb

Devinweb is a web app agency in Tetouan, Morocco. [our website](http://devinweb.com).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
