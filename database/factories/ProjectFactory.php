<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Genre;
use App\Models\ProjectType;
use App\Models\User;
use App\Traits\Factory\HasFakeHelperFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
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
            'name' => fake()->realText('50'),
            'placeholder' => $this->getFakePlaceholderByNum(rand(1, 10)),
            'description' => fake()->realText,
            'start_date' => $start = fake()->dateTimeBetween('-30 days', '+60 days'),
            'deadline' => fake()->dateTimeBetween(
                $start->format('Y-m-d') . '+5 days',
                $start->format('Y-m-d') . '+10 days'
            ),
            'status' => fake()->randomElement(array_column(ProjectStatus::cases(), 'value')),
            'user_id' => User::factory(),
            'genre_id' => Genre::select('id')->inRandomOrder()->value('id'),
            'project_type_id' => ProjectType::select('id')->inRandomOrder()->value('id'),
        ];
    }

    private function getFakePlaceholderByNum(int $number): string
    {
        $path = storage_path('seeders/project/placeholders/placeholder_' . $number . '.jpg');

        return 'data:image/avif;base64,' . base64_encode(file_get_contents($path));
    }
}
