<?php

namespace Devinweb\Payment\Traits\HyperPay;

use Illuminate\Support\Arr;

/**
 * 
 */
trait Helpers
{
    /**
     * @param array $parameters
     * @return string $params
     */
    public function getParameters(array $parameters)
    {
        $params = '';
        foreach ($parameters as $key => $value) {
            $value = (array) $value;
            $params .= '&' . $value['name'] . '=' . urlencode($value['value']);
        }

        return $params;
    }

    /**
     * 
     * @param array $response
     * 
     */

    public function hyperPayresponse(array $response)
    {
        $successPattern = '/^(000\.000\.|000\.100\.1|000\.[36])/';
        $manuallPattern = '/^(000\.400\.0[^3]|000\.400\.100)/';
        if (preg_match($successPattern, Arr::get($response, 'result.code')) || preg_match($manuallPattern, Arr::get($response, 'result.code'))) {
            return $this->successCallback($response);
        } else {
            return $this->errorCallback($response);
        }
    }


    /**
     * 
     * @param array $response
     * @return \Illuminate\Http\Response
     */

    public function successCallback(array $response)
    {
        if (Arr::has($response, 'threeDSecure')) {
            return redirect(Arr::get($this->config, 'callback_urls.success') . '?' . http_build_query($response));
        }
        return response()->json(['success' => ['url' => url(Arr::get($this->config, 'callback_urls.success') . '?' . http_build_query($response)), 'response' => $response]]);
    }


    /**
     * 
     * @param array $response
     * @return \Illuminate\Http\Response
     */

    public function errorCallback(array $response)
    {
        if (Arr::has($response, 'threeDSecure')) {
            return redirect(Arr::get($this->config, 'callback_urls.error') . '?' . http_build_query($response));
        }
        return response()->json(['error' => ['url' => url(Arr::get($this->config, 'callback_urls.error') . '?' . http_build_query($response)), 'response' => $response]]);
    }
}
