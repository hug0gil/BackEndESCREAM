<?php

namespace App\Http\Controllers;

use App\Models\Subgenre;
use Illuminate\Http\Request;

class SubGenresController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query("per_page", 10);
        $users = Subgenre::paginate($perPage);

        return response()->json($users);
    }
}
