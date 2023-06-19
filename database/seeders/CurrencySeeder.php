<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! Currency::exists()) {
            Currency::insert($this->data());
        }
    }

    private function data(): array
    {
        return [
            [
                'name' => 'Euro',
                'code' => 'eur',
                'symbol' => '€',
            ],
            [
                'name' => 'Dollar',
                'code' => 'usd',
                'symbol' => '$',
            ],
        ];
    }
}
