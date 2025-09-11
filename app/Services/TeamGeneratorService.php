<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use DomainException;

/**
 * For the sake of time I utilized ChatGPT to help work out the solution for this service
 * Here lies the actual business logic in generating the teams.
 * 
 * My initial though process was using the number of goalies to determine the number of teams.
 * However this fails because even though there is 5 goalies for example, that doesn't necessarily equal 5 teams because of the criteria that the number of players per team need to be within a specific bounds.
 * Their will be situations where multiple players that can be a goalie might be on the same team.
 * 
 * This solution uses a greedy algorithm in order to determine where to place the players based on ranking and team size
 */

class TeamGeneratorService
{
    public function generate(Collection $players, array $options = []): array
    {
        $min   = $options['min']   ?? 18;
        $max   = $options['max']   ?? 22;
        $avg   = $options['prefer_avg'] ?? (($min + $max) / 2);

        if ($players->isEmpty()) {
            throw new DomainException('No players available.');
        }

        // Split and count
        $goalies = $players->where('can_play_goalie', true)->values();
        $N = $players->count();
        $G = $goalies->count();

        // Decide T (# of teams)
        $T = $this->pickTeamCount($N, $G, $min, $max, $avg);
        // Target sizes per team
        [$targets, $base, $rem] = $this->targetSizes($N, $T);

        // Seed one goalie per team (shuffle goalies so it’s fair/random)
        $seeded = $goalies->shuffle()->take($T)->values();

        // Initialize team buckets
        $teams = [];
        for ($i = 0; $i < $T; $i++) {
            $g = $seeded[$i];
            $teams[$i] = [
                'name'        => null,
                'players'     => collect([$g]),
                'rank_sum'    => (int) $g->ranking,
                'target_size' => $targets[$i],
            ];
        }

        // Pool = everyone else (including extra goalies) sorted by ranking DESC
        $assignedIds = $seeded->pluck('id')->all();
        $pool = $players
            ->reject(fn($p) => in_array($p->id, $assignedIds, true))
            ->sortByDesc('ranking')
            ->values();

        // Greedy balance fill
        $this->assignGreedy($teams, $pool);


        // Finalize arrays + totals
        $resultTeams = array_map(function ($t) {
            $players = $t['players']->map(fn($p) => [
                'id'              => $p->id,
                'first_name'      => $p->first_name,
                'last_name'       => $p->last_name,
                'ranking'         => (int) $p->ranking,
                'can_play_goalie' => (bool) $p->can_play_goalie,
            ])->values()->all();

            return [
                'name'          => $t['name'],
                'players'       => $players,
                'total_ranking' => array_sum(array_column($players, 'ranking')),
            ];
        }, $teams);

        // Give names
        $names = $this->randomTeamNames($T);
        foreach ($resultTeams as $i => &$team) {
            $team['name'] = $names[$i];
        }

        // Save + return
        $id = (string) Str::uuid();

        return [
            'id'    => $id,
            'teams' => $resultTeams,
            'meta'  => [
                'N' => $N, 'G' => $G, 'T' => $T,
                'target_sizes' => $targets,
                'base' => $base, 'remainder' => $rem,
            ],
        ];
    }

    /** Choose T respecting all constraints. */
    protected function pickTeamCount(
        int $N, int $G, int $min, int $max, float $preferAvg): int {
        $tMin = (int) ceil($N / $max);
        $tMax = (int) floor($N / $min);

        $candidates = [];
        for ($t = $tMin; $t <= $tMax; $t++) {
            if ($t % 2 !== 0) continue;       // even teams only
            if ($t > $G) continue;            // at least one goalie per team
            $candidates[] = $t;
        }

        if (empty($candidates)) {
            throw new DomainException("Cannot form even teams of {$min}-{$max} with {$N} players and {$G} goalie(s).");
        }

        // prefer average size close to preferAvg (e.g., 20)
        usort($candidates, fn($a, $b) => abs($N / $a - $preferAvg) <=> abs($N / $b - $preferAvg));
        return $candidates[0];
    }

    /** Compute target sizes per team (distribute remainder). */
    protected function targetSizes(int $N, int $T): array
    {
        $base = intdiv($N, $T);
        $rem  = $N % $T;
        $targets = [];
        for ($i = 0; $i < $T; $i++) {
            $targets[$i] = $base + ($i < $rem ? 1 : 0);
        }
        return [$targets, $base, $rem];
    }

    /** Greedy assignment: always give the next player to the lowest-sum team with room. */
    protected function assignGreedy(array &$teams, Collection $pool): void
    {
        foreach ($pool as $p) {
            $best = null; $bestSum = PHP_INT_MAX;
            foreach ($teams as $i => $t) {
                if ($t['players']->count() >= $t['target_size']) continue; // full
                if ($t['rank_sum'] < $bestSum) {
                    $best = $i; $bestSum = $t['rank_sum'];
                }
            }
            // safety: if all at target, place on min-sum team (shouldn't happen)
            if ($best === null) {
                $best = array_keys(array_column($teams, 'rank_sum'), min(array_column($teams, 'rank_sum')))[0];
            }
            $teams[$best]['players']->push($p);
            $teams[$best]['rank_sum'] += (int) $p->ranking;
        }
    }

    protected function randomTeamNames(int $count): array
    {
        $faker = fake();
        $adjs  = ['Crimson','Royal','Mighty','Rapid','Valiant','Iron','Golden','Emerald','Azure','Shadow','Prime','Northern','Western','Central','Wild','Thunder','Bright','Silver','Bold','Lone'];
        $nouns = ['Lions','Hawks','Wolves','Ravens','Sharks','Titans','Owls','Panthers','Eagles','Spartans','Rangers','Pioneers','Falcons','Stallions','Comets','Coyotes','Giants','Dragons','Bulls','Foxes'];

        $combos = [];
        foreach ($adjs as $a) foreach ($nouns as $n) $combos[] = "$a $n";

        $take  = min($count, count($combos));
        $names = $faker->randomElements($combos, $take, false);

        while (count($names) < $count) {
            $faker->unique(true);
            $names[] = \Illuminate\Support\Str::title($faker->unique()->words(2, true));
        }
        return $names;
    }
}
