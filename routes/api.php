<?php

use Illuminate\Support\Facades\Route;
use Devinweb\Payment\Http\Controllers\Payment\TransactionController;
use Devinweb\Payment\Http\Controllers\Payment\FormController;
use Illuminate\Http\Request;


/**
 * ------------------
 * payfort routes
 * ------------------
 */
Route::post(config('payments.routes.submit-payment'), 'Devinweb\Payment\Http\Controllers\Payment\TransactionController@handle');
Route::match(['get', 'post'], '/api/{provider}/response', 'Devinweb\Payment\Http\Controllers\Payment\TransactionController@paymentResponse');
Route::match(['get', 'post'], '/api/{provider}/process-response', 'Devinweb\Payment\Http\Controllers\Payment\TransactionController@processPaymentPresponse');
Route::match(['get', 'post'], '/api/{provider}/web-hook', 'Devinweb\Payment\Http\Controllers\Payment\TransactionController@webHookNotify');

/**
 * ------------------
 * Payfort callback routes
 * ------------------
 */
Route::get(config('payments.payfort.callback_urls.success-page'), function (Request $request) {
    return view('payment::success');
})->name('success');

Route::get(config('payments.payfort.callback_urls.error-page'), function (Request $request) {
    return view('payment::error');
})->name('error');
