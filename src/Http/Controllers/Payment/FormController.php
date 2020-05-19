<?php

namespace Devinweb\Payment\Http\Controllers\Payment;

use Devinweb\Payment\Facades\Payment;
use Devinweb\Payment\Http\Controllers\Controller;
use Devinweb\Payment\Http\Requests\VerifyPaymentData;
use Illuminate\Http\Request;

class FormController extends Controller
{


    /**
     * 
     */
    public function show()
    {
        return view('payment::form');
    }


    /**
     * 
     */
    public function withoutValidation(Request $request)
    {
        $merchant_reference = Payment::generateMerchantReference();

        return Payment::pay($request, $merchant_reference);
    }
}
