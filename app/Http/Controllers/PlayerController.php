<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class PlayerController extends Controller
{
    public function index(): JsonResponse
    {
        // uses your scopeOfPlayers() from the User model
        $players = User::ofPlayers()
            ->orderBy('ranking', 'desc')
            ->where('user_type', "player")
            ->get();

        return response()->json($players);
    }

    // public function balancedTeams(): JsonResponse
    // {
    //     # Requirements:
    //     # generate balanced teams where each team has between 18-22 players and there is an even number of teams
    //     # each team should have at least one goalie
    //     # make sure the total ranking per team is as even as possible
    //     # generate random names for each team
        
    //     $players = User::ofPlayers()
    // }
}
