<?php

namespace Devinweb\Payment\Tests\Feature;

use Devinweb\Payment\Tests\TestUser;
use Faker\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Devinweb\Payment\PaymentServiceProvider;
use Devinweb\Payment\Http\Requests\VerifyPaymentData;
use Devinweb\Payment\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class PaymentFormTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Http\Requests\VerifyPaymentData */
    private $rules;

    /** @var \Illuminate\Validation\Validator */
    private $validator;

    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        Config::set("payfort-payment.merchantIdentifier", "merchantIdentifier");
        Config::set("payfort-payment.accessCode", "accessCode");
        Config::set("payfort-payment.SHARequestPhrase", "SHARequestPhrase");
        Config::set("payfort-payment.SHAResponsePhrase", "SHAResponsePhrase");
        $this->validator = app()->get('validator');
        $this->rules = (new VerifyPaymentData())->rules();

        // $this->loadLaravelMigrations();
    }


    /** @test */
    public function form_is_valid()
    {

        $data = [
            'card_number' => '4005550000000001',
            'expiration_year' => '2021',
            'expiration_month' => '05',
            'cvc' => '123',
            'hold_name' => 'darbaoui imad',
            'email' => 'imad@devinweb.com',
            'amount' => 480
        ];
        $user = $this->init_user_data();

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);

        $response = $this->actingAs($user, 'api')->json('POST', config('payfort-payment.routes.submit-payment'), $data);
        // $response->assertJson($data);
        $response->assertStatus(201);
    }
















    public function init_user_data()
    {
        $faker  = Factory::create();
        return TestUser::create([
            'name' => $faker->name,
            'email' => $email = $faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // password
            'remember_token' => Str::random(10),
        ]);
    }
}
