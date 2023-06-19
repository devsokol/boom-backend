<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Role;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = Role::factory(2)
            ->hasRoleMaterials(2)
            ->hasPickShootingDates(5);

        Project::factory(15)
            ->has($roles)
            ->create();
    }
}
