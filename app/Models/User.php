<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class User extends Model
{
    public $timestamps = false;

    // Let Eloquent cast your ints/bools automatically
    protected function casts(): array
    {
        return [
            'ranking' => 'integer',
            'can_play_goalie' => 'boolean',
        ];
    }

    // Include virtual attributes when serializing to array/JSON
    protected $appends = ['is_goalie', 'fullname'];

    /** Players only local scope */
    public function scopeOfPlayers(Builder $query): Builder
    {
        return $query->where('user_type', 'player');
    }

    // Old-style accessors you provided (still supported)
    public function getIsGoalieAttribute(): bool
    {
        return (bool) $this->can_play_goalie;
    }

    public function getFullnameAttribute(): string
    {
        return Str::title($this->first_name . ' ' . $this->last_name);
    }
}
