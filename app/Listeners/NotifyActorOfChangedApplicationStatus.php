<?php

namespace App\Listeners;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Modules\ApiV1\Notifications\ApprovedNotification;
use Modules\ApiV1\Notifications\AuditionNotification;
use Modules\ApiV1\Notifications\RejectedNotification;
use Modules\ApiV1\Notifications\RoleOfferNotification;
use Modules\ApiV1\Notifications\SelfTapeRequestNotification;
use Modules\MobileV1\Notifications\RoleAcceptedNotification;
use Modules\MobileV1\Notifications\RoleDeclinedNotification;

class NotifyActorOfChangedApplicationStatus
{
    private Application $application;

    public function handle(object $event): void
    {
        $this->application = $event->application;

        //if ($this->application->isDirty('status')) {
        $this->sendNotification();
        //}
    }

    private function sendNotification(): void
    {
        switch ($this->application->status) {
            case ApplicationStatus::APPROVED:
                $this->application->role->project->user->notify(new RoleAcceptedNotification($this->application));

                break;

            case ApplicationStatus::APPROVAL:
                $this->application->actor->notify(new ApprovedNotification($this->application));

                break;

            case ApplicationStatus::REJECTED_BY_OWNER:
                $this->application->actor->notify(new RejectedNotification($this->application));

                break;

            case ApplicationStatus::REJECTED_BY_ACTOR:
                $this->application->role->project->user->notify(new RoleDeclinedNotification($this->application));

                break;

            case ApplicationStatus::AUDITION:
                $this->application->actor->notify(new AuditionNotification($this->application));

                break;

            case ApplicationStatus::SELFTAPE_REQUEST:
                $this->application->actor->notify(new SelfTapeRequestNotification($this->application));

                break;

            case ApplicationStatus::ROLE_OFFER:
                $this->application->actor->notify(new RoleOfferNotification($this->application));

                break;
        }
    }
}
