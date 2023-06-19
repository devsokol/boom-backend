<?php

namespace Modules\MobileV1\Events;

use App\Models\Actor;
use Illuminate\Queue\SerializesModels;

class ActorRegisteredEvent
{
    use SerializesModels;

    public Actor $actor;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Actor $actor)
    {
        $this->actor = $actor;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
