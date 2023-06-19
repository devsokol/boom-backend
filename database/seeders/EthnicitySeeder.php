<?php

namespace Database\Seeders;

use App\Models\Ethnicity;
use Illuminate\Database\Seeder;

class EthnicitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! Ethnicity::exists()) {
            Ethnicity::insert([
                ['name' => '{"en":"White / European descent", "no":"Hvit / Europeisk avstamning"}'],
                ['name' => '{"en":"Latino / Hispanic", "no":"Latino / Hispanic"}'],
                ['name' => '{"en":"Black / African descent", "no":"Svart / Afrikansk avstamning"}'],
                ['name' => '{"en":"South asian / Indian", "no":"Sør-asiatisk / Indisk"}'],
                ['name' => '{"en":"Southeast asian / Pacific islander", "no":"Sørøst-asiatisk / Stillehavsøyboer"}'],
                ['name' => '{"en":"Ethnically Ambiguous / Multiracial", "no":"Etnisk tvetydig / Flerrase"}'],
            ]);
        }
    }
}
