<?php

namespace App\Dto;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class AuditionData extends Data
{
    public function __construct(
        public int $type,
        public string $address,
        public Carbon $audition_datetime,
        public ?array $materials,
    )
    {
    }
}
