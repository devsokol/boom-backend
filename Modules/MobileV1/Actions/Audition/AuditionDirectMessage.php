<?php

namespace Modules\MobileV1\Actions\Audition;

use App\Models\Application;
use Illuminate\Support\Facades\Mail;
use Modules\MobileV1\Emails\AuditionDirectMessageMail;

class AuditionDirectMessage
{
    public function handle(Application $application, ?string $message): void
    {
        $actorName = $application->audition->application->actor->getFullName();

        $projectOwnerEmail = $application->audition->application->role->project->user->email;

        Mail::to($projectOwnerEmail)->send(new AuditionDirectMessageMail($actorName, $message));
    }
}
