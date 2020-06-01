<?php

namespace Devinweb\Payment\Tests;

use Devinweb\Payment\PaymentServiceProvider;
use Faker\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        // $this->loadLaravelMigrations();
    }

    protected function getPackageProviders($app)
    {
        return [PaymentServiceProvider::class];
    }


    public function getEnvironmentSetUp($app)
    {

        // include_once __DIR__ . '/../database/migrations/create_payment_transactions_table.php.stub';
        // (new \CreatePaymentTransactionsTable)->up();
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
