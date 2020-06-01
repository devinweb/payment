<?php

namespace Devinweb\Payment\Services;


interface PaymentInterface
{
    /**
     * User the payment service to process the payment.
     * @var string $driver
     * @var string $merchant_reference
     */
    public function use($driver, $merchant_reference);
}
