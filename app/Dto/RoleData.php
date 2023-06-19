<?php

namespace App\Dto;

use Spatie\LaravelData\Data;

class RoleData extends Data
{
    public function __construct(
        public string $name,
        public string $description,
        public ?int $rate,
        public ?string $city,
        public ?string $address,
        public ?string $application_deadline,
        public ?int $pick_shooting_date_type,
        public ?array $pick_shooting_dates,
        public ?array $acting_gender,
        public ?int $min_age,
        public ?int $max_age,
        public ?array $personal_skills,
        public ?array $materials,
        public ?int $country_id,
        public ?int $currency_id,
        public int $payment_type_id,
        public ?int $ethnicity_id,
        public ?array $headshots,
        public ?array $selftapes,
        public int $status = 0,
    )
    {
    }
}
