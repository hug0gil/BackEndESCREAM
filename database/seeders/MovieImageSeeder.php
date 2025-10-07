<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class MovieImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = Movie::all();

        foreach ($movies as $movie) {

            $response = Http::get('https://api.themoviedb.org/3/search/movie', [
                'api_key' => env('TMDB_API_KEY'),
                'query' => $movie->title,
                'year' => $movie->year
            ]);

            $data = $response->json();

            if (!empty($data['results'][0]['poster_path'])) {
                $movie->image = 'https://image.tmdb.org/t/p/w500' . $data['results'][0]['poster_path'];
                $movie->save();
            }
        }
    }
}
