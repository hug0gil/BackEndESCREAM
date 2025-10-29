<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Movie;
use App\Models\Plan;
use App\Models\Subgenre;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class MoviesController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->query("per_page", 10);
        $movies = Movie::paginate($perPage);

        return response()->json($movies);
    }

    public function show(Movie $movie)
    {
        // Cargamos las relaciones para obtener datos importantes y relacionados con la movie
        $movie->load(['actors', 'director', 'productionCompany', 'subgenres']);
        return response()->json(["movieData" => $movie], Response::HTTP_OK);
    }

    public function update(UpdateMovieRequest $updateMovieRequest, Movie $movie)
    {

        try {
            // Actualiza campos de la tabla principal
            $movie->update($updateMovieRequest->validated());

            // Actualiza actores si vienen en la request
            if ($updateMovieRequest->has('actor_ids')) {
                $movie->actors()->sync($updateMovieRequest->validated('actor_ids', []));
            }

            // Actualiza subgéneros si vienen en la request
            if ($updateMovieRequest->has('subgenre_ids')) {
                $movie->subgenres()->sync($updateMovieRequest->validated('subgenre_ids', []));
            }

            return response()->json(["message" => "Movie updated successfully!", $movie], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json(["errors" => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy(Movie $movie)
    {
        try {
            $movie->delete();
            return response()->json(["message" => "Movie deleted successfully"], Response::HTTP_OK);
        } catch (QueryException $e) {
            Log::error('Failed to delete movie', [
                'movie_id' => $movie->id,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode()
            ]);

            return response()->json(["error" => "Cannot delete movie due to database constraints"], Response::HTTP_CONFLICT);
        }
    }

    public function getAllPlans()
    {
        $plans = Plan::all();
        return response()->json($plans, Response::HTTP_OK);
    }

    public function getImage(Movie $movie)
    {
        return redirect()->away($movie->image);
    }

    public function getMoviePerSubgenre(string $slug)
    {
        $subgenre = Subgenre::where('slug', $slug)->first();

        if (!$subgenre) {
            return response()->json(['message' => 'Subgenre not found'], Response::HTTP_NOT_FOUND);
        }

        $movies = $subgenre->movies()->with(['actors', 'director', 'productionCompany', 'subgenres'])->get();
        /*
        $subgenre->movies() = movies del subgenero
        with($campos) = traemos los datos importantes de las peliculas
        */

        return response()->json($movies, Response::HTTP_OK);
    }
}
