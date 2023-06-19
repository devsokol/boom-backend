<?php

namespace Database\Seeders;

use App\Models\PersonalSkill;
use Illuminate\Database\Seeder;

class PersonalSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! PersonalSkill::exists()) {
            PersonalSkill::insert([
                ['name' => '{"en":"Juggling"}'],
                ['name' => '{"en":"Horseback riding"}'],
                ['name' => '{"en":"Close-up magic"}'],
                ['name' => '{"en":"Fencing"}'],
                ['name' => '{"en":"Boxing"}'],
                ['name' => '{"en":"Singing"}'],
                ['name' => '{"en":"Stage combat"}'],
                ['name' => '{"en":"Gymnastics"}'],
                ['name' => '{"en":"Basketball"}'],
                ['name' => '{"en":"Baseball"}'],
                ['name' => '{"en":"Soccer"}'],
                ['name' => '{"en":"Handball"}'],
                ['name' => '{"en":"Hockey"}'],
                ['name' => '{"en":"Ice skating"}'],
                ['name' => '{"en":"Figure skating"}'],
                ['name' => '{"en":"Volleyball"}'],
                ['name' => '{"en":"Football"}'],
                ['name' => '{"en":"Swimming"}'],
                ['name' => '{"en":"Swimming (competitive)"}'],
                ['name' => '{"en":"Diving"}'],
                ['name' => '{"en":"Skiing"}'],
                ['name' => '{"en":"Bowling"}'],
                ['name' => '{"en":"Roller skating"}'],
                ['name' => '{"en":"Surfing"}'],
                ['name' => '{"en":"Firearms training"}'],
                ['name' => '{"en":"Darts"}'],
                ['name' => '{"en":"Cheerleading"}'],
                ['name' => '{"en":"Mime"}'],
                ['name' => '{"en":"Ventriloquism"}'],
                ['name' => '{"en":"Standup comedy"}'],
                ['name' => '{"en":"Beatboxing"}'],
                ['name' => '{"en":"Rapping"}'],
                ['name' => '{"en":"Guitar"}'],
                ['name' => '{"en":"Piano"}'],
                ['name' => '{"en":"Drums"}'],
                ['name' => '{"en":"Bass"}'],
                ['name' => '{"en":"Flute"}'],
                ['name' => '{"en":"Driving a car"}'],
                ['name' => '{"en":"Driving a bus"}'],
                ['name' => '{"en":"Driving a truck"}'],
                ['name' => '{"en":"Driving a motorcycle"}'],
                ['name' => '{"en":"Airplane control"}'],
                ['name' => '{"en":"Helicopter control"}'],
                ['name' => '{"en": "Sign language"}'],
            ]);
        }
    }
}
