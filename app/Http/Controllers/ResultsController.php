<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class ResultsController extends Controller
{
    public function show(Request $request): InertiaResponse|\Illuminate\Http\JsonResponse
    {
        $id = (string) $request->query('id', '');
        $teams = Cache::get("teams:$id");

        return Inertia::render('Results', [
            'id'    => $id,
            'teams' => $teams,
        ]);
    }
}
