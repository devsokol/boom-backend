<?php

namespace Modules\MobileV1\Listeners;

use Modules\MobileV1\Events\ActorRegisteredEvent;

class SendVerificationCodeToActorListener
{
    /**
     * Handle the event.
     *
     * @param  ActorRegisteredEvent  $event
     * @return void
     */
    public function handle(ActorRegisteredEvent $event): void
    {
        $event->actor->sendVerificationCode();
    }
}
