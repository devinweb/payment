<?php

namespace Devinweb\Payment\Traits\Payfort;

use Illuminate\Support\Arr;

trait ApplePay
{
    /** 
     * Get the Apple Pay params 
     * 
     * @return array  
     */

    public function getDataParams()
    {
        $apple_header = (array) $this->request->apple_header;
        $apple_paymentMethod = (array) $this->request->apple_paymentMethod;
        $apple_paymentMethod['apple_type'] = $apple_paymentMethod['apple_type'] === 'PKPaymentMethodTypeDebit' ? 'debit' : $apple_paymentMethod['apple_type'];
        $apple_paymentMethod['apple_type'] = $apple_paymentMethod['apple_type'] === 'PKPaymentMethodTypeCredit' ? 'credit' : $apple_paymentMethod['apple_type'];

        ksort($apple_header);
        ksort($apple_paymentMethod);
        $params = [
            "access_code"           => Arr::get($this->config, 'accessCode'),
            "amount"                => intval(floatval($this->request->amount) * 100),
            "apple_data"            => $this->request->apple_data,
            "apple_header"          => $apple_header,
            "apple_paymentMethod"   => $apple_paymentMethod,
            "apple_signature"       => $this->request->apple_signature,
            "apple_version"         => $this->request->apple_version ?? "EC_v1",
            "command"               => Arr::get($this->config, 'command'),
            "currency"              => Arr::get($this->config, 'currency'),
            "customer_email"        => $this->request->email,
            "digital_wallet"        => $this->request->digital_wallet,
            "language"              => Arr::get($this->config, 'language'),
            "merchant_identifier"   => Arr::get($this->config, 'merchantIdentifier'),
            "merchant_reference"    => $this->request->get('merchant_reference'),
        ];
        return $params;
    }



    /**
     * calculate fort signature
     * @param array $arrData
     * @param string $signType request or response
     * @return string fort signature
     */
    public function calculateSignature(array $arrData, $signType = 'request')
    {
        $shaString             = '';

        foreach ($arrData as $key => $val) {
            if ($key === 'signature') {
                continue;
            }
            if (gettype($val) !== 'array') {
                $shaString .= "$key=$val";
            } else {
                $sub_str = $key . '={';
                $index = 0;
                // ksort($val);
                foreach ($val as $k => $v) {
                    $sub_str .= "$k=$v";
                    if ($index < count($val) - 1) {
                        $sub_str .= ', ';
                    }
                    $index++;
                }
                $sub_str .= '}';
                $shaString .= $sub_str;
            }
        }
        if ($signType == 'request') {
            $shaString = Arr::get($this->config, 'SHARequestPhrase') . $shaString . Arr::get($this->config, 'SHARequestPhrase');
        } else {
            $shaString = Arr::get($this->config, 'SHAResponsePhrase') . $shaString . Arr::get($this->config, 'SHAResponsePhrase');
        }

        $signature = hash(Arr::get($this->config, 'SHAType'), $shaString);

        return $signature;
    }
}
