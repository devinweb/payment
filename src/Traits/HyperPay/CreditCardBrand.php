<?php

namespace Devinweb\Payment\Traits\HyperPay;



/**
 * 
 */
trait CreditCardBrand
{
    protected $supported_cards = [
        self::BRAND_VISA => '/^4\d{12}(\d{3})?$/',
        self::BRAND_MASTERCARD => self::REGEX_MASTERCARD,
        self::BRAND_DISCOVER => '/^(6011|65\d{2}|64[4-9]\d)\d{12}|(62\d{14})$/',
        self::BRAND_AMEX => '/^3[47]\d{13}$/',
        self::BRAND_DINERS_CLUB => '/^3(0[0-5]|[68]\d)\d{11}$/',
        self::BRAND_JCB => '/^35(28|29|[3-8]\d)\d{12}$/',
        self::BRAND_SWITCH => '/^6759\d{12}(\d{2,3})?$/',
        self::BRAND_SOLO => '/^6767\d{12}(\d{2,3})?$/',
        self::BRAND_DANKORT => '/^5019\d{12}$/',
        self::BRAND_MAESTRO => '/^(5[06-8]|6\d)\d{10,17}$/',
        self::BRAND_FORBRUGSFORENINGEN => '/^600722\d{10}$/',
        self::BRAND_LASER => '/^(6304|6706|6709|6771(?!89))\d{8}(\d{4}|\d{6,7})?$/',
    ];

    /**
     * Credit Card Brand
     *
     * Iterates through known/supported card brands to determine the brand of this card
     *
     * @return string
     */
    public function getBrand($number)
    {
        foreach ($this->supported_cards as $brand => $val) {
            if (preg_match($val, $number)) {
                return $brand;
            }
        }
    }
}
