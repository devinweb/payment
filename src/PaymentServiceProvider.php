<?php

namespace Devinweb\Payment;

// use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Devinweb\Payment\Http\Controllers\Payment\TransactionController;
// use Illuminate\Http\Request;
class PaymentServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'payment');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'payment');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');




        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/payments.php' => config_path('payments.php'),
            ], 'config');

            // if (!class_exists('CreatePaymentTransactionsTable')) {
            //     $this->publishes([
            //         __DIR__ . '/../database/migrations/create_payment_transactions_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_payment_transactions_table.php'),
            //     ], 'migrations');;
            // }



            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/payment'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/payment'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/payment'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {

        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/payments.php', 'payments');

        // Register the main class to use with the facade
        $this->app->singleton('payment', function ($app) {
            return new Payment($app);
        });
    }
}
