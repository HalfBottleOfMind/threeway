<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pivot'
    ];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class, 'achievement_tournament_user');
    }

    public function getUsersAttribute()
    {
        $users = $this->users()
            ->withPivot('achievement_id')
            ->get();

        $achievements = Achievement::find(
            $users->pluck('pivot.achievement_id')->filter()
        );

        $users = $users->groupBy('id');

        $users->each(fn ($user, $key) => $users[$key] = collect(
            $user->first()
        )->except('pivot')->union([
            'achievements' => $achievements->whereIn(
                'id',
                $user->pluck('pivot.achievement_id')->filter()
            ),
            'points' => $achievements->whereIn(
                'id',
                $user->pluck('pivot.achievement_id')->filter()
            )->sum('cost')
        ]));

        return $users->values();
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'achievement_tournament_user');
    }

    public function achievementPoints(User $user = null)
    {
        $query = $this->achievements();
        if ($user) {
            $query = $query->withPivot('user_id')
                ->where('pivot_user_id', $user->id);
        }
        return $query->get()->sum('cost');1
    }
}
