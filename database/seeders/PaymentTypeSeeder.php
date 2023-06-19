<?php

namespace Database\Seeders;

use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! PaymentType::exists()) {
            PaymentType::insert([
                ['name' => '{"en":"Hour", "no":"Time"}', 'is_single' => false],
                ['name' => '{"en":"Day", "no":"Dag"}', 'is_single' => false],
                ['name' => '{"en":"Week", "no":"Uke"}', 'is_single' => false],
                ['name' => '{"en":"Month", "no":"Måned"}', 'is_single' => false],
                ['name' => '{"en":"Year", "no":"År"}', 'is_single' => false],
                ['name' => '{"en":"Fixed fee", "no":"Fast avgift"}', 'is_single' => false],
                ['name' => '{"en":"Not paid", "no":"Fast avgift"}', 'is_single' => true],
                ['name' => '{"en":"Upon agreement", "no":"Fast avgift"}', 'is_single' => true],
            ]);
        }
    }
}
