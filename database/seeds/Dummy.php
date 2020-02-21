<?php

use App\Achievement;
use App\Tournament;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Dummy extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('achievement_tournament_user')->delete();
        DB::table('achievements')->delete();
        DB::table('tournaments')->delete();
        DB::table('users')->delete();

        factory(User::class, 10)->create();
        factory(Tournament::class, 10)->create();
        factory(Achievement::class, 10)->create();

        $user = User::first();
        $tournament = Tournament::first();
        $user->tournaments()->attach($tournament);
        $user->achievements()->attach(Achievement::all(), ['tournament_id' => $tournament->id]);

        $user = User::all()[1];
        $tournament = Tournament::first();
        $user->tournaments()->attach($tournament);
        $user->achievements()->attach(Achievement::all(), ['tournament_id' => $tournament->id]);

        $user = User::all()[2];
        $tournament = Tournament::first();
        $user->tournaments()->attach($tournament);
        $user->achievements()->attach(Achievement::all(), ['tournament_id' => $tournament->id]);
    }
}
