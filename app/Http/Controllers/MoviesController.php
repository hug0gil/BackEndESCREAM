<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Movie;
use App\Models\Plan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
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

    public function store(CreateMovieRequest $createMovieRequest)
    {
        try {
            $movie = Movie::create($createMovieRequest->validated());

            if (isset($createMovieRequest['actor_ids'])) {
                $movie->actors()->sync($createMovieRequest['actor_ids']);
            }

            if (isset($createMovieRequest['subgenre_ids'])) {
                $movie->subgenres()->sync($createMovieRequest['subgenre_ids']);
            }

            return response()->json(["message" => "Movie created successfully!", "movie_id" => $movie->id, "movie" => $movie], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json(["errors" => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function show(Movie $movie)
    {
        if (!$movie) {
            return response()->json(["error" => "Movie not found"], Response::HTTP_NOT_FOUND);
        }
        return response()->json([$movie], Response::HTTP_OK);
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
            return response()->json(["error" => "Cannot delete movie due to database constraints"], Response::HTTP_CONFLICT);
        }
    }

    public function getAllPlans()
    {
        $plans = Plan::all();
        return response()->json($plans, Response::HTTP_OK);
    }
}
