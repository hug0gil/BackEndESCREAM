<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMovieRequest;
use App\Models\Movie;
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

            return response()->json(["message" => "Movie created successfully!", "movie" => $movie], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json(["errors" => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
