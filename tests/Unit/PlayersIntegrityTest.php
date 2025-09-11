<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PlayersIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGoaliePlayersExist () 
    {
/*
		Check there are players that have can_play_goalie set as 1   
*/
		$result = User::ofPlayers()->where('can_play_goalie', 1)->count();
		$this->assertTrue($result > 1);
	
    }
    public function testAtLeastOneGoaliePlayerPerTeam () 
    {
/*
	    calculate how many teams can be made so that there is an even number of teams and they each have between 18-22 players.
	    Then check that there are at least as many players who can play goalie as there are teams
*/
        $players = User::ofPlayers()->get();
        $goalies = $players->where('can_play_goalie', 1);
        $totalPlayers = $players->count();
        $totalGoalies = $goalies->count();
        $minPlayersPerTeam = 18;
        $maxPlayersPerTeam = 22;
        $minTeams = (int) ceil($totalPlayers / $maxPlayersPerTeam);
        $maxTeams = (int) floor($totalPlayers / $minPlayersPerTeam);
        $maxPossibleTeams = 0;

        for ($teamCount = $minTeams; $teamCount <= $maxTeams; $teamCount++) {
            if ($teamCount % 2 === 0) {
                $maxPossibleTeams = $teamCount;
            }
        }
        
        $this->assertGreaterThanOrEqual(
            $maxPossibleTeams, 
            $totalGoalies,
            "Need at least {$maxPossibleTeams} goalies to form {$maxPossibleTeams} teams, but only have {$totalGoalies} goalies"
        );
    }
}
