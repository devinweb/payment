<?php

namespace Devinweb\Payment\Http\Controllers\Payment;

use Devinweb\Payment\Facades\Payment;
use Devinweb\Payment\Http\Controllers\Controller;
use Devinweb\Payment\Http\Requests\VerifyPaymentData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransactionController extends Controller
{

    public function __construct()
    {
        //
    }

    /**
     * 
     */
    public function handle(Request $request)
    {
        $merchant_reference = Payment::use('payfort')->generateMerchantReference();
        $payfort =  Payment::use('payfort',  "$merchant_reference")->pay();
        return response()->json($payfort, 201);
    }

    /**
     * after TOKINIZATION payfort return to this endpoint with an response
     * @var \Illuminate\Http\Request
     */
    public function paymentResponse(Request $request, $provider)
    {
        return Payment::use($provider)->responseCallback();
    }

    /**
     * handle the response returned from payfort.
     * @param \Illuminate\Http\Request
     */
    public function processPaymentPresponse(Request $request, $provider)
    {
        return Payment::use($provider)->processResponseCallback();
    }


    /**
     * handle the response returned from payfort.
     * @var \Illuminate\Http\Request
     */
    public function webHookNotify(Request $request, $provider)
    {
        return Payment::use($provider)->webHook();
    }
}
