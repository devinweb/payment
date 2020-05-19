<?php

namespace Devinweb\Payment\Tests\Feature;

use Devinweb\Payment\Tests\TestUser;
use Faker\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Devinweb\Payment\PaymentServiceProvider;
use Illuminate\Support\Facades\Hash;
use Devinweb\Payment\Http\Requests\VerifyPaymentData;
use Devinweb\Payment\Models\PaymentTransaction;
use Devinweb\Payment\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class PaymentDatabaseTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function it_can_access_to_database()
    {
        $uuid = Str::uuid()->toString();
        $transation = PaymentTransaction::create([
            "id" => $uuid,
            "hold_name" => "darbaoui imad",
            "email" => "imad@devinweb.com",
            "status" => "pending",
            "query" => "[]",
        ]);


        $new_transaction = PaymentTransaction::first();
        $this->assertSame($new_transaction->id, $uuid);
    }
}
