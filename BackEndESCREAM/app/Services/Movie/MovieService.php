<?php

namespace App\Services\Movie;

use App\Models\Movie;
use App\Models\Plan;
use App\Models\Subgenre;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class MovieService
{
    public function getAllPaginated(int $perPage = 10)
    {
        return Movie::paginate($perPage);
    }

    public function getMovieWithRelations(Movie $movie)
    {
        return $movie->load(['actors', 'director', 'productionCompany', 'subgenres']);
    }

    public function updateMovie(Movie $movie, array $validatedData): Movie
    {
        $movie->update($validatedData);

        if (isset($validatedData['actor_ids'])) {
            $movie->actors()->sync($validatedData['actor_ids']);
        }

        if (isset($validatedData['subgenre_ids'])) {
            $movie->subgenres()->sync($validatedData['subgenre_ids']);
        }

        return $movie;
    }

    public function deleteMovie(Movie $movie): bool
    {
        try {
            $movie->delete();
            return true;
        } catch (QueryException $e) {
            Log::error('Failed to delete movie', [
                'movie_id' => $movie->id,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode()
            ]);
            return false;
        }
    }

    public function getAllPlans()
    {
        return Plan::all();
    }

    public function getMoviesBySubgenre(string $slug): ?object
    {
        $subgenre = Subgenre::where('slug', $slug)->first();

        if (!$subgenre) {
            return null;
        }

        return $subgenre->movies()
            ->with(['actors', 'director', 'productionCompany', 'subgenres'])
            ->get();
    }
}
