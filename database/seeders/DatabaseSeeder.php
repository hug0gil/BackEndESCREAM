<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //User::factory(3)->create();

        $plansData = [
            [
                'id' => 1,
                'name' => 'Gloomy', // Basic
                'price' => 6.99,
                'devices_allowed' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Sinister', // Essential
                'price' => 12.99,
                'devices_allowed' => 2,
            ],
            [
                'id' => 3,
                'name' => 'Horrifying', // Premium
                'price' => 15.99,
                'devices_allowed' => 4,
            ],
        ];

        foreach ($plansData as $plan) {
            Plan::updateOrCreate(['id' => $plan['id']], $plan);
        }


        User::factory()->create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'password123',
            'plan_id' => 1,
        ]);

        User::factory()->create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'password' => 'secret456',
            'plan_id' => 2,
        ]);

        User::factory()->create([
            'name' => 'Charlie',
            'email' => 'charlie@example.com',
            'password' => 'mypassword789',
            'plan_id' => 3,
        ]);

        $this->call(AccountsDefaultSeeder::class);
    }
}
