<?php

namespace Devinweb\Payment\Traits\Payfort;

use Illuminate\Support\Arr;

trait PayfortServices
{
    /**
     * calculate fort signature
     * @param array $arrData
     * @param string $signType request or response
     * @return string fort signature
     */
    public function calculateSignature(array $arrData, $signType = 'request')
    {
        $shaString             = '';
        ksort($arrData);
        foreach ($arrData as $k => $v) {
            $shaString .= "$k=$v";
        }

        if ($signType == 'request') {
            $shaString = Arr::get($this->config, 'SHARequestPhrase') . $shaString . Arr::get($this->config, 'SHARequestPhrase');
        } else {
            $shaString = Arr::get($this->config, 'SHAResponsePhrase') . $shaString . Arr::get($this->config, 'SHAResponsePhrase');
        }
        $signature = hash(Arr::get($this->config, 'SHAType'), $shaString);

        return $signature;
    }


    public function getUrl($path)
    {
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $url = $scheme . $_SERVER['HTTP_HOST'] . $path;
        return $url;
    }


    /**
     * Convert Amount with dicemal points
     * @param decimal $amount
     * @param string  $currencyCode
     * @return decimal
     */
    public function convertFortAmount($amount, $currencyCode)
    {
        $new_amount = 0;
        $total = $amount;
        $decimalPoints    = $this->getCurrencyDecimalPoints($currencyCode);
        $new_amount = round($total, $decimalPoints) * (pow(10, $decimalPoints));
        return $new_amount;
    }


    /**
     * 
     * @param string $currency
     * @param integer 
     */
    public function getCurrencyDecimalPoints($currency)
    {
        $decimalPoint  = 2;
        $arrCurrencies = array(
            'JOD' => 3,
            'KWD' => 3,
            'OMR' => 3,
            'TND' => 3,
            'BHD' => 3,
            'LYD' => 3,
            'IQD' => 3,
        );
        if (isset($arrCurrencies[$currency])) {
            $decimalPoint = $arrCurrencies[$currency];
        }
        return $decimalPoint;
    }



    /**
     * Log the error on the disk
     */
    public function log($messages)
    {
    }


    /**
     * 
     */
    public function postMessageReactNative($message)
    {
        $webview_to_react = "setTimeout(function(){ window.ReactNativeWebView.postMessage(JSON.stringify($message)); }, 3000);";
        echo '<html>
				<head>
                    <meta charset="utf-8">
                    <meta data-n-head="true" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" user-scalable="no">
                    <style>
                            @-webkit-keyframes scale { 0% { -webkit-transform: scale(1); transform: scale(1); opacity: 1; } 45% { -webkit-transform: scale(0.1); transform: scale(0.1); opacity: 0.7; } 80% { -webkit-transform: scale(1); transform: scale(1); opacity: 1; } }
                            @keyframes scale { 0% { -webkit-transform: scale(1); transform: scale(1); opacity: 1; } 45% { -webkit-transform: scale(0.1); transform: scale(0.1); opacity: 0.7; } 80% { -webkit-transform: scale(1); transform: scale(1); opacity: 1; } }
                            .ball-pulse > div:nth-child(0) { -webkit-animation: scale 0.75s -0.36s infinite cubic-bezier(0.2, 0.68, 0.18, 1.08); animation: scale 0.75s -0.36s infinite cubic-bezier(0.2, 0.68, 0.18, 1.08); }
                            .ball-pulse > div:nth-child(1) { -webkit-animation: scale 0.75s -0.24s infinite cubic-bezier(0.2, 0.68, 0.18, 1.08); animation: scale 0.75s -0.24s infinite cubic-bezier(0.2, 0.68, 0.18, 1.08); }
                            .ball-pulse > div:nth-child(2) { -webkit-animation: scale 0.75s -0.12s infinite cubic-bezier(0.2, 0.68, 0.18, 1.08); animation: scale 0.75s -0.12s infinite cubic-bezier(0.2, 0.68, 0.18, 1.08); }
                            .ball-pulse > div:nth-child(3) { -webkit-animation: scale 0.75s 0s infinite cubic-bezier(0.2, 0.68, 0.18, 1.08); animation: scale 0.75s 0s infinite cubic-bezier(0.2, 0.68, 0.18, 1.08); }
                            .ball-pulse > div { background-color: #fff; width: 15px; height: 15px; border-radius: 100%; margin: 2px; -webkit-animation-fill-mode: both; animation-fill-mode: both; display: inline-block; }
                            #overlay{ display: flex; background: #00bf8f; background: -webkit-linear-gradient(to left, #00bf8f , #001510); background: linear-gradient(to left, #00bf8f , #001510); justify-content: center; align-items: center; align-content: center; position: fixed; z-index: 1031; top: 0; right: 0; left: 0; bottom: 0; }
                            .loa-icon{ color:#fff; }
                            .loa-icon .loader-inner>div{ background-color:#fff; width: 20px; height: 20px; }
                            .txt-center{ text-align:center; }
                            .h1-checkout{ text-align:center; color:#fff; }
                            .msg-alert-checkout{ text-align:center; color:#fff; }
                    </style>
				</head>
				<body>
					<div id="overlay"> <span> <div class="txt-center loa-icon"> <div class="loader-inner ball-pulse"> <div></div> <div></div> <div></div> </div> </div> </span> </div>
				</body>
                            <script>
                            document.addEventListener("DOMContentLoaded", function(event) { 
                                ' . $webview_to_react . '
                            });
                            
                    </script>
                </body>
            </html>
        ';
        exit;
    }
}
