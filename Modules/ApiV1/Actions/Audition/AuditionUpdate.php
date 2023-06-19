<?php

namespace Modules\ApiV1\Actions\Audition;

use App\Models\Audition;

class AuditionUpdate
{
    public function handle(Audition $audition, array $data): bool
    {
        return $audition->update($data);
    }
}
