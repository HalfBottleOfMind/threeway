<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Many-to-many relation to Tournament
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'achievement_tournament_user');
    }

    /**
     * Return collection of tournaments which this user played.
     * Each element of collection has 'achievements' collection, represents all
     * achievements, that this user get during this tournament
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getTournamentsAttribute()
    {
        $tournaments = $this->tournaments()
                    ->withPivot('achievement_id')
                    ->get()
                    ->groupBy('id');

        $achievements = $this->achievements->unique();

        $tournaments->each(function ($tournament, $key) use ($tournaments, $achievements) {
            $tournaments[$key] = collect($tournament->first())->except('pivot')->union([
                'achievements' => $achievements->whereIn(
                    'id',
                    $tournament->pluck('pivot.achievement_id')->filter()
                )
            ]);
        });

        return $tournaments->values();
    }

    /**
     * Many-to-many relation to Achievement
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'achievement_tournament_user');
    }

    /**
     * Return sum of achievement points in specified tournament or alltime
     * achievement points
     *
     * @param Tournament $tournanent
     *
     * @return void
     */
    public function achievementPoints(Tournament $tournanent = null)
    {
        $builder = $this->achievements();
        if ($tournanent) {
            $builder = $builder->withPivot('tournament_id')
                                ->where('pivot_tournament_id', $tournanent->id);
        }
        return $builder->get()->sum('cost');
    }


}
