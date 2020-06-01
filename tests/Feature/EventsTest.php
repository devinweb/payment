<?php

namespace Devinweb\Payment\Tests\Feature;

use Devinweb\Payment\Events\FailedTransaction;
use Devinweb\Payment\Events\SuccessTransaction;
use Devinweb\Payment\Services\Payfort\PayfortProvider;
use Devinweb\Payment\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Mockery as m;

class EventsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        // $this->loadLaravelMigrations();

        Config::set("payments.payfort.merchantIdentifier", "merchantIdentifier");
        Config::set("payments.payfort.accessCode", "accessCode");
        Config::set("paymenst.payfort.SHARequestPhrase", "SHARequestPhrase");
        Config::set("payments.payfort.SHAResponsePhrase", "SHAResponsePhrase");
    }


    // public function getEnvironmentSetUp($app)
    // {

    //     include_once __DIR__ . '/../../database/migrations/create_payment_transactions_table.php.stub';
    //     (new \CreatePaymentTransactionsTable)->up();
    // }

    // /** @test */
    // function an_event_is_emitted_when_a_transaction_request()
    // {

    //     Event::fake();

    //     $user = $this->init_user_data();

    //     $data = [
    //         'card_number' => '4005550000000001',
    //         'expiration_year' => '2021',
    //         'expiration_month' => '05',
    //         'cvc' => '123',
    //         'hold_name' => 'darbaoui imad',
    //         'email' => 'imad@devinweb.com',
    //         'amount' => 480
    //     ];


    //     $response = $this->actingAs($user, 'api')->json('POST', config('payfort-payment.routes.submit-payment'), $data);
    //     $response->assertStatus(201);
    //     $merchant_reference = $response['params']['merchant_reference'];

    //     // Event::assertDispatched(TransactionCreated::class, function ($event) use ($merchant_reference) {
    //     //     return $event->payment_transaction['merchant_reference'] === $merchant_reference;
    //     // });
    // }



    // /** @test */
    // function an_event_is_emitted_when_a_confirmed_transaction()
    // {
    //     $faker  = Factory::create();

    //     Event::fake();


    //     $payfort_response = [
    //         'response_code' => '18000',
    //         'card_number' => '400555******0001',
    //         'card_holder_name' => 'darbaoui imad',
    //         'signature' => 'd641d71c13da959cba92371d70c686b602e2b62796dfca5286c760c6b5d9e3b1',
    //         'merchant_identifier' => $faker->swiftBicNumber,
    //         'expiry_date' => '2105',
    //         'access_code' => $faker->password,
    //         'language' => 'ar',
    //         'service_command' => 'TOKENIZATION',
    //         'response_message' => 'عملية ناجحة',
    //         'merchant_reference' => '278245857',
    //         'token_name' => 'dced12c0eeeb444185dcc450b917d987',
    //         'return_url' => 'http://payment-test.test/api/payfort-response',
    //         'card_bin' => '400555',
    //         'status' => '18',
    //     ];



    //     $response = $this->json('GET', config('payfort-payment.routes.payfort-response'), $payfort_response);
    //     $response->assertStatus(302);
    //     Event::assertDispatched(FailedTransaction::class, function ($event) use ($payfort_response) {
    //         return $event->payment_transaction['merchant_reference'] === $payfort_response['merchant_reference'];
    //     });
    // }

    /** @test */
    public function it_receive_a_success_transaction_from_payfort()
    {
        Event::fake();

        $payfort_response = [
            'response_code' => '18000',
            'card_number' => '400555******0001',
            'card_holder_name' => 'darbaoui imad',
            'signature' => 'e272f917ce8cd92dbb178dff59a706b989203ddcbe3aaf48bb1524d7bfa67e5d',
            'merchant_identifier' => config('payments.payfort.merchantIdentifier'),
            'expiry_date' => '2105',
            'access_code' => config('payments.payfort.accessCode'),
            'language' => 'ar',
            'service_command' => 'TOKENIZATION',
            'response_message' => 'عملية ناجحة',
            'merchant_reference' => '278245857',
            'token_name' => 'dced12c0eeeb444185dcc450b917d987',
            'return_url' => 'http://payment.return-url',
            'card_bin' => '400555',
            'status' => '18',
        ];

        $request = m::mock(Request::class);
        $request->shouldReceive('get')->with('signature')->andReturn($payfort_response['signature']);
        $request->shouldReceive('get')->with('response_code')->andReturn($payfort_response['response_code']);
        $request->shouldReceive('toArray')->andReturn($payfort_response);
        (new PayfortProvider($request, config('payments.payfort'), '278245857'))->webHook();
        Event::assertDispatched(SuccessTransaction::class, function ($event) use ($payfort_response) {
            return $event->payment_transaction['merchant_reference'] === $payfort_response['merchant_reference'];
        });
    }


    /** @test */
    public function it_receive_a_fail_transaction_from_payfort()
    {
        Event::fake();

        $payfort_response = [
            "response_code" => "00016",
            "card_number" => "400550******0001",
            "card_holder_name" => "CUSTOMER_HOLDER_NAME",
            'signature' => '40f9274b023bfcc5951238a9c9e5d80b5ed82128f0a3dbeb153d848a79361c09',
            'merchant_identifier' => config('payments.payfort.merchantIdentifier'),
            "expiry_date" => "2105",
            'access_code' => config('payments.payfort.accessCode'),
            "language" => "ar",
            "service_command" => "TOKENIZATION",
            "response_message" => "رقم البطاقة غير صحيح",
            "merchant_reference" => "158151963",
            'return_url' => 'http://payment.return-url',
            "status" => "00",
            "error_msg" => "رقم البطاقة غير صحيح",
        ];

        $request = m::mock(Request::class);
        $request->shouldReceive('get')->with('signature')->andReturn($payfort_response['signature']);
        $request->shouldReceive('get')->with('response_code')->andReturn($payfort_response['response_code']);
        $request->shouldReceive('toArray')->andReturn($payfort_response);
        (new PayfortProvider($request, config('payments.payfort'), '158151963'))->webHook();
        Event::assertDispatched(FailedTransaction::class, function ($event) use ($payfort_response) {
            return $event->payment_transaction['merchant_reference'] === $payfort_response['merchant_reference'];
        });
    }
}
