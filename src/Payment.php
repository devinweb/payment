<?php

namespace Devinweb\Payment;

use Devinweb\Payment\Services\Payfort\PayfortProvider;
use Devinweb\Payment\Services\Payfort\PayfortApplePayProvider;
use Devinweb\Payment\Services\PaymentInterface;
use Illuminate\Support\Manager;
use InvalidArgumentException;
use Illuminate\Support\Facades\Log;

class Payment extends Manager implements PaymentInterface
{
    /**
     * @var string $merchant_reference  
     */
    protected $merchant_reference;


    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @param  string  $merchant_reference
     * @return mixed
     */
    public function use($driver, $transaction_id = null)
    {
        $this->merchant_reference = $transaction_id;
        $this->drivers[$driver] = null;
        return $this->driver($driver);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Devinweb\Payment\Services\AbstractProvider
     */
    protected function createPayfortDriver()
    {
        $config = $this->app['config']['payments.payfort'];
        return $this->buildProvider(
            PayfortProvider::class,
            $config,
            $this->merchant_reference
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Devinweb\Payment\Services\AbstractProvider
     */
    protected function createPayfortApplePayDriver()
    {
        $config = $this->app['config']['payments.payfort_apple_pay'];

        return $this->buildProvider(
            PayfortApplePayProvider::class,
            $config,
            $this->merchant_reference
        );
    }

    /**
     * Build a provider instance.
     *
     * @param  string  $provider
     * @param  array  $config
     * @return \Devinweb\Payment\Services\AbstractProvider
     */
    public function buildProvider($provider, $config, $merchant_reference)
    {
        return new $provider(
            $this->app['request'],
            $config,
            $merchant_reference
        );
    }


    /**
     * Get the default driver name.
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No Payment gateway was specified.');
    }
}
