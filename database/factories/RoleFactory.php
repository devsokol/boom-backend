<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\RoleStatus;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Ethnicity;
use App\Models\PaymentType;
use App\Models\PersonalSkill;
use App\Models\Role;
use App\Traits\Factory\HasFakeHelperFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class RoleFactory extends Factory
{
    use HasFakeHelperFactory;

    public function configure()
    {
        return $this->afterCreating(function (Role $role) {
            $randomPersonalSkillIds = PersonalSkill::query()
                ->select('id')
                ->get()
                ->random(rand(1, 4))
                ->pluck('id')
                ->toArray();

            $role->personalSkills()->sync($randomPersonalSkillIds);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->sentence(),
            'description' => fake()->realText(50),
            'rate' => fake()->randomDigitNotZero(),
            'city' => fake()->city(),
            'address' => fake()->streetAddress(),
            'status' => fake()->randomElement(array_column(RoleStatus::cases(), 'value')),
            'acting_gender' => fake()->randomElement(array_column(Gender::cases(), 'value')),
            'min_age' => rand(5, 18),
            'max_age' => rand(32, 90),
            'application_deadline' => fake()->dateTimeBetween('+70 days', '+80 days'),
            'country_id' => Country::select('id')->inRandomOrder()->value('id'),
            'currency_id' => Currency::select('id')->inRandomOrder()->value('id'),
            'ethnicity_id' => Ethnicity::select('id')->inRandomOrder()->value('id'),
            'payment_type_id' => PaymentType::select('id')->inRandomOrder()->value('id'),
        ];
    }
}
