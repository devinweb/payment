<?php

namespace Devinweb\Payment\Tests\Unit;

use Devinweb\Payment\Facades\Payment;
use Devinweb\Payment\Tests\TestCase;
use Faker\Factory;
use Illuminate\Support\Facades\Config;

class SignatureTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        // $this->loadLaravelMigrations();

        Config::set("payfort-payment.merchantIdentifier", "merchantIdentifier");
        Config::set("payfort-payment.accessCode", "accessCode");
        Config::set("payfort-payment.SHARequestPhrase", "SHARequestPhrase");
        Config::set("payfort-payment.SHAResponsePhrase", "SHAResponsePhrase");
    }


    /** @test */

    public function it_can_calculate_signature_request()
    {
        $params = [
            "key_1" => "hprohaska",
            "key_2" => "deangelo.dietrich",
            "key_3" => "zfunk",
            "key_4" => "ijohnson",
            "key_5" => "lmann",
            "key_6" => "tillman.cierra",
            "key_7" => "norma45",
            "key_8" => "kub.unique",
            "key_9" => "ricardo85",
            "key_10" => "luther.kuphal",
        ];

        $signature = Payment::calculateSignature($params, 'request');
        $this->assertSame("fb84ca44db306fe1de213d36db52d8cd6db4d6c2410e843e9ae7033e128a33eb", $signature);
    }

    /** @test */

    public function it_can_calculate_signature_response()
    {
        $params = [
            "key_1" => "hprohaska",
            "key_2" => "deangelo.dietrich",
            "key_3" => "zfunk",
            "key_4" => "ijohnson",
            "key_5" => "lmann",
            "key_6" => "tillman.cierra",
            "key_7" => "norma45",
            "key_8" => "kub.unique",
            "key_9" => "ricardo85",
            "key_10" => "luther.kuphal",
        ];

        $signature = Payment::calculateSignature($params, 'response');
        $this->assertSame("46c134b80cde243973a8ca99a3cb5b81280643a3272d203332b161c35c517770", $signature);
    }


    /** @test */

    public function it_should_fail_if_type_not_defined_on_calculate_signature()
    {
        $params = [];
        $faker  = Factory::create();
        foreach (range(1, 10) as $number) {
            array_push($params, ["key_$number" => $faker->unique()->userName]);
        }
        try {
            Payment::calculateSignature($params, '');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertEquals($e->getMessage(), 'The given data was invalid.');
        }
    }
}
