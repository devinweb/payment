<?php

namespace Devinweb\Payment\Tests\Feature;

use Devinweb\Payment\Tests\TestUser;
use Faker\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Devinweb\Payment\Http\Requests\VerifyPaymentData;
use Devinweb\Payment\Services\Payfort\PayfortProvider;
use Devinweb\Payment\Services\Payfort\PayfortResponseRequest;
use Devinweb\Payment\Tests\TestCase;
use Illuminate\Contracts\Session\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery as m;
use InvalidArgumentException;

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
        Config::set("payments.payfort.merchantIdentifier", "merchantIdentifier");
        Config::set("payments.payfort.accessCode", "accessCode");
        Config::set("payments.payfort.sandboxMode", true);
        Config::set("payments.payfort.SHARequestPhrase", "SHARequestPhrase");
        Config::set("payments.payfort.SHAResponsePhrase", "SHAResponsePhrase");
        $this->validator = app()->get('validator');
        $this->rules = (new VerifyPaymentData())->rules();

        // $this->loadLaravelMigrations();
    }

    public function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_run_exception_if_email_not_provided()
    {

        $data = [
            'card_number' => '4005550000000001',
            'expiration_year' => '2021',
            'expiration_month' => '05',
            'cvc' => '123',
            'hold_name' => 'darbaoui imad',
            'email' => null,
            'amount' => 480
        ];
        $merchant_reference = '158151963';
        $mockrequest = m::mock(Request::class);
        $mockrequest->shouldReceive('get')->with('amount')->andReturn($data['amount']);
        $mockrequest->shouldReceive('get')->with('email')->andReturn($data['email']);
        $mockrequest->shouldReceive('get')->with('hold_name')->andReturn($data['hold_name']);
        $mockrequest->shouldReceive('add')->andReturn(['merchant_reference' => $merchant_reference]);
        $mockrequest->shouldReceive('toArray')->andReturn($data);
        try {
            (new PayfortProvider($mockrequest, config('payments.payfort'), $merchant_reference))->pay();
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals($e->getMessage(), 'the customer email is required !');
        }
    }

    /** @test */
    public function it_run_exception_if_hold_name_not_provided()
    {

        $data = [
            'card_number' => '4005550000000001',
            'expiration_year' => '2021',
            'expiration_month' => '05',
            'cvc' => '123',
            'hold_name' => null,
            'email' => 'darbaoui@gmail.com',
            'amount' => 480
        ];
        $merchant_reference = '158151963';
        $mockrequest = m::mock(Request::class);
        $mockrequest->shouldReceive('get')->with('amount')->andReturn($data['amount']);
        $mockrequest->shouldReceive('get')->with('email')->andReturn($data['email']);
        $mockrequest->shouldReceive('get')->with('hold_name')->andReturn($data['hold_name']);
        $mockrequest->shouldReceive('add')->andReturn(['merchant_reference' => $merchant_reference]);
        $mockrequest->shouldReceive('toArray')->andReturn($data);
        try {
            (new PayfortProvider($mockrequest, config('payments.payfort'), $merchant_reference))->pay();
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals($e->getMessage(), 'the customer hold_name is required !');
        }
    }

    /** @test */
    public function it_run_exception_if_amount_not_provided()
    {

        $data = [
            'card_number' => '4005550000000001',
            'expiration_year' => '2021',
            'expiration_month' => '05',
            'cvc' => '123',
            'hold_name' => 'darbaoui imad',
            'email' => 'imad@devinweb.com',
            'amount' => null
        ];
        $merchant_reference = '158151963';
        $mockrequest = m::mock(Request::class);
        $mockrequest->shouldReceive('get')->with('amount')->andReturn($data['amount']);
        $mockrequest->shouldReceive('get')->with('email')->andReturn($data['email']);
        $mockrequest->shouldReceive('get')->with('hold_name')->andReturn($data['hold_name']);
        $mockrequest->shouldReceive('add')->andReturn(['merchant_reference' => $merchant_reference]);
        $mockrequest->shouldReceive('toArray')->andReturn($data);
        try {
            (new PayfortProvider($mockrequest, config('payments.payfort'), $merchant_reference))->pay();
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals($e->getMessage(), 'the amount is required !');
        }
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
        $merchant_reference = '158151963';
        $mockrequest = m::mock(Request::class);
        $mockrequest->shouldReceive('get')->with('amount')->andReturn($data['amount']);
        $mockrequest->shouldReceive('get')->with('email')->andReturn($data['email']);
        $mockrequest->shouldReceive('get')->with('hold_name')->andReturn($data['hold_name']);
        $mockrequest->shouldReceive('add')->andReturn(['merchant_reference' => $merchant_reference]);
        $mockrequest->shouldReceive('toArray')->andReturn($data);
        $response = (new PayfortProvider($mockrequest, config('payments.payfort'), $merchant_reference))->pay();
        $this->assertArrayHasKey('form', $response);
        $this->assertArrayHasKey('url', $response);
        $this->assertArrayHasKey('params', $response);
        $this->assertEquals($merchant_reference, $response['params']['merchant_reference']);
    }

    /** @test */
    public function if_empty_tokenization_response()
    {
        $payfort_tokenization_response = [];
        $redirect_url  = url(config('payments.payfort.callback_urls.error-page')) . '?' . http_build_query([
            "error_msg" => "Invalid Response Parameters",
        ]);
        $response = $this
            ->get("/api/payfort/response?" . http_build_query($payfort_tokenization_response));

        $response->assertRedirect($redirect_url);
        $response->assertStatus(302);
    }

    /** @test */
    public function it_success_tokenization_response()
    {
        $payfort_tokenization_response = [
            'response_code' => '18000',
            'card_number' => '400555******0001',
            'card_holder_name' => 'darbaoui imad',
            'signature' => 'c53e30ba3b12e3e8090c6e20820d34a21498a0b88d24e92c4ddd4a0ee09454bc',
            'merchant_identifier' => config('payments.payfort.merchantIdentifier'),
            'expiry_date' => '2105',
            'access_code' => config('payments.payfort.accessCode'),
            'language' => 'en',
            'service_command' => 'TOKENIZATION',
            'response_message' => 'Success',
            'merchant_reference' => '992941778',
            'token_name' => '3876026064af4e6fb70984a637520e07',
            'return_url' => 'http://return.url',
            'card_bin' => '400555',
            'status' => '18',
        ];
        $response =  $this->call('GET', "/api/payfort/response?" . http_build_query($payfort_tokenization_response), ['REMOTE_ADDR' => '127.0.0.1']);
        $this->assertEquals(302, $response->status());
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
