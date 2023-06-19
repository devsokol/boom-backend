<?php

namespace Modules\MobileV1\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\MobileV1\Events\ActorRegisteredEvent;
use Modules\MobileV1\Listeners\SendVerificationCodeToActorListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ActorRegisteredEvent::class => [
            SendVerificationCodeToActorListener::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
