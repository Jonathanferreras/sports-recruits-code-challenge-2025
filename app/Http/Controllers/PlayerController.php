<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class PlayerController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $players = User::ofPlayers()
            ->orderBy('ranking', 'desc')
            ->where('user_type', "player")
            ->get();

        if ($players->isEmpty()) {
            return response()->json(['message' => 'No Players available'], 422);
        }

        return response()->json($players);
        
        } catch (\DomainException $e) {
            return response()->json(['message' => 'Error occurred while fetching players'], 500);
        }

    }
}
