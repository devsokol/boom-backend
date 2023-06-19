<?php

namespace Database\Factories;

use App\Traits\Factory\HasFakeHelperFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
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
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'avatar' => $this->avatarBase64(),
            'company_name' => fake()->company(),
            'phone_number' => fake()->e164PhoneNumber(),
            'email' => fake()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'Ss12345678_#',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
