<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'Türker Jöntürk',
            'email' => 'turkerjonturk@case.com',
            'since' => '2014-06-28',
            'revenue' => '492.12'
        ]);

        User::factory()->create([
            'name' => 'Kaptan Devopuz',
            'email' => 'kaptandevopuz@case.com',
            'since' => '2015-01-15',
            'revenue' => '1505.95'
        ]);

        User::factory()->create([
            'name' => 'İsa Sonuyumaz',
            'email' => 'isasonuyumaz@case.com',
            'since' => '2016-02-11',
            'revenue' => '0.00'
        ]);
    }
}
