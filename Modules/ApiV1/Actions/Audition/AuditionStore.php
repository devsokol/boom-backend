<?php

namespace Modules\ApiV1\Actions\Audition;

use App\Dto\AuditionData;
use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Audition;
use Illuminate\Support\Facades\DB;

class AuditionStore
{
    public function handle(AuditionData $data, Application $application): Audition
    {
        $data = collect($data->toArray());

        $application->load('audition');

        return DB::transaction(function () use ($data, $application) {
            if ($application->audition) {
                $application->audition->delete();
            }

            $audition = $application
                ->audition()
                ->create($data->except('materials')->toArray());

            $materials = $data->get('materials');

            if ($materials) {
                $audition->auditionMaterials()->createMany($materials);
            }

            $audition->load('auditionMaterials');

            $application->update(['status' => ApplicationStatus::AUDITION->value]);

            return $audition;
        });
    }
}
