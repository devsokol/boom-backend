<?php

namespace App\Dto;

use Spatie\LaravelData\Data;

class ApplicationSelftapeData extends Data
{
    public function __construct(
        public string $description,
        public string $deadline_datetime,
        public ?array $materials
    )
    {
    }
}
