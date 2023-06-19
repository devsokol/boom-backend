<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use Illuminate\Database\Seeder;

class ProjectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! ProjectType::exists()) {
            ProjectType::insert([
                ['name' => '{"en":"Feature film", "no":"Spillefilm"}'],
                ['name' => '{"en":"Short film", "no":"Kortfilm"}'],
                ['name' => '{"en":"Music video", "no":"Musikkvideo"}'],
                ['name' => '{"en":"Commercial", "no":"Kommersiell"}'],
                ['name' => '{"en":"Documentary", "no":"Dokumentar"}'],
                ['name' => '{"en":"Other", "no":"Annen"}'],
            ]);
        }
    }
}
