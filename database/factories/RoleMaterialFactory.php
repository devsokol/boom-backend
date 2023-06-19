<?php

namespace Database\Factories;

use App\Models\MaterialType;
use App\Traits\Factory\HasFakeHelperFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class RoleMaterialFactory extends Factory
{
    use HasFakeHelperFactory;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'attachment' => $this->getTestPdfFile(),
            'material_type_id' => MaterialType::select('id')->inRandomOrder()->value('id'),
        ];
    }
}
