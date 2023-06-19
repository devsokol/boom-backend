<?php

namespace Modules\ApiV1\Actions\Application;

use App\Enums\ApplicationStatus;

class ApplicationStatusesList
{
    public function handle(): mixed
    {
        return collect(ApplicationStatus::cases())->reduce(function ($carry, $status) {
            $carry[$status->value] = $status->name;

            return $carry;
        }, []);
    }
}
