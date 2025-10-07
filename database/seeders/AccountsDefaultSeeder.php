<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Profile;
use App\Models\Review;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountsDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profilesData = [
            [
                'id' => 1,
                'user_id' => 1,
                'profile_name' => 'Hugo',
                'age_restriction' => 18,
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'profile_name' => 'Julio',
                'age_restriction' => 12,
            ],
            [
                'id' => 3,
                'user_id' => 1,
                'profile_name' => 'Ana',
                'age_restriction' => 16,
            ],

            [
                'id' => 4,
                'user_id' => 2,
                'profile_name' => 'Celeste',
                'age_restriction' => 16,
            ],
            [
                'id' => 5,
                'user_id' => 2,
                'profile_name' => 'Leo',
                'age_restriction' => 18,
            ],
            [
                'id' => 6,
                'user_id' => 2,
                'profile_name' => 'Luis',
                'age_restriction' => 12,
            ],

            [
                'id' => 7,
                'user_id' => 3,
                'profile_name' => 'Tomás',
                'age_restriction' => 18,
            ],
            [
                'id' => 8,
                'user_id' => 3,
                'profile_name' => 'Sofía',
                'age_restriction' => 16,
            ],
            [
                'id' => 9,
                'user_id' => 3,
                'profile_name' => 'Lucas',
                'age_restriction' => 12,
            ],
        ];


        foreach ($profilesData as $profile) {
            Profile::updateOrCreate(['id' => $profile['id']], $profile);
        }

        $this->call(HorrorBlockbustersSeeder::class);
        $this->call(MovieImageSeeder::class);


        $reviewsData = [
            [
                'id' => 1,
                'user_id' => 1,
                'movie_id' => 1,
                'rating' => 5,
                'comment' => 'Amazing movie! A must-watch.',
                'date' => '2017-10-01',
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'movie_id' => 2,
                'rating' => 4,
                'comment' => 'Great storyline and characters.',
                'date' => '2023-10-02',
            ],
            [
                'id' => 3,
                'user_id' => 1,
                'movie_id' => 3,
                'rating' => 3,
                'comment' => null,
                'date' => '2025-10-03',
            ],
        ];

        foreach ($reviewsData as $review) {
            Review::updateOrCreate(['id' => $review['id']], $review);
        }
    }
}
