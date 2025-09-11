<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TeamGeneratorService;
use Illuminate\Http\Request;

class TeamsController extends Controller
{
    public function __construct(private TeamGeneratorService $generator) {}

    public function generate(Request $request)
    {
        try {
            $players = User::ofPlayers()->get(['id','first_name','last_name','ranking','can_play_goalie']);
            $result = $this->generator->generate($players, [
                'min' => 18,
                'max' => 22,
                'prefer_avg' => 20,
            ]);

            return response()
            ->json(['id' => $result['id']], 201)
            ->header('Location', route('results.show', ['id' => $result['id']]));
        } 
        catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
