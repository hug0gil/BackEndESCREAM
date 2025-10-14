<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Review;
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
                'year' => $movie->year,
                'language' => 'es-ES'
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data['results'][0]['poster_path'])) {
                    $movie->image = 'https://image.tmdb.org/t/p/w500' . $data['results'][0]['poster_path'];
                    $movie->save();
                    $this->command->info("Guardada imagen para {$movie->title}");
                } else {
                    $this->command->warn("No se encontró imagen para {$movie->title}");
                }
            } else {
                $this->command->error("Error con la API para {$movie->title}");
            }

            sleep(0.5);
        }
    }
}


/*
Request para obtener las películas

   $url = 'https://api.themoviedb.org/3/search/movie';
            $params = [
                'api_key' => env('TMDB_API_KEY'),
                'query' => $movie->title,
                'year' => $movie->year,
                'language' => 'es-ES'
            ];

            $responseAPI = $url . '?' . http_build_query($params);

            $response = Http::get($responseAPI);
            $this->command->info("URL de petición: {$responseAPI}");


Request para obtener imagen (wSize)

https://image.tmdb.org/t/p/w500


A la hora de autenticarnos para la API

| Tipo             | Qué es                            | Uso típico                                                                                                                                            |
| ---------------- | --------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------- |
| **API Key**      | Clave pública                     | Identifica tu aplicación o proyecto a la API. Se usa para acceder a datos públicos (ej. buscar películas). No está ligada a un usuario.               |
| **Bearer Token** | Token privado / de acceso usuario | Representa a un usuario autenticado. Se usa para acceder a datos privados o hacer cambios en nombre del usuario (ej. crear listas, marcar favoritos). |


*/