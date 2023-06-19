<?php

namespace App\Services\Sms;

use App\Services\Sms\Gateways\InsightsGateway;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SmsClient::class, function () {
            switch (config('sms.client')) {
                default:
                    $gateway = new InsightsGateway(
                        config('sms.insights.access_token'),
                        config('sms.insights.alpha')
                    );
            }

            return new SmsClient($gateway);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
