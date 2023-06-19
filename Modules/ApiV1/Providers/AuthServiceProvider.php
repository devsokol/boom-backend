<?php

namespace Modules\ApiV1\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        //
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}
