<?php

namespace App\Jobs;

use App\Models\Actor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ActorSmoothDeleting implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private Actor $actor)
    {
    }

    public function handle(): void
    {
        if ($this->actor->isMarkAsDeleted()) {
            $this->actor->delete();
        }
    }
}
