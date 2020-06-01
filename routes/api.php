<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


/**
 * ------------------
 * payfort routes
 * ------------------
 */
Route::match(['get', 'post'], '/api/{provider}/response', 'Devinweb\Payment\Http\Controllers\Payment\TransactionController@paymentResponse');
Route::match(['get', 'post'], '/api/{provider}/process-response', 'Devinweb\Payment\Http\Controllers\Payment\TransactionController@processPaymentPresponse');
Route::match(['get', 'post'], '/api/{provider}/web-hook', 'Devinweb\Payment\Http\Controllers\Payment\TransactionController@webHookNotify');
