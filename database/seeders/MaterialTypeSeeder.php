<?php

namespace Database\Seeders;

use App\Models\MaterialType;
use Illuminate\Database\Seeder;

class MaterialTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! MaterialType::exists()) {
            MaterialType::insert([
                ['name' => '{"en":"Reference image", "no":"Referansebilde"}'],
                ['name' => '{"en":"Audition script", "no":"Audition-manus"}'],
                ['name' => '{"en":"Video clip", "no":"Videoklipp"}'],
                ['name' => '{"en":"Other", "no":"Annen"}'],
            ]);
        }
    }
}
