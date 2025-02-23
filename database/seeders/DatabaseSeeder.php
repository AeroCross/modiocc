<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\User;
use \App\Models\Game;
use \App\Models\Mod;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // These are meant to be users that allow the test suite to run.
        $firstUser = User::factory()
            ->has(Game::factory()->count(3))
            ->create([
                'name' => 'Test User A',
                'email' => 'test_a@example.com',
                'password' => 'asdf'
            ]);

        $firstUser->tokens()->create([
            'name' => 'modio',
            'token' => hash('sha256', 'asdf'),
        ]);

        Mod::factory()->count(10)->create([
            'user_id' => $firstUser->id,
            'game_id' => $firstUser->games->first()->id,
        ]);

        $secondUser = User::factory()
            ->has(Game::factory()->count(5))
            ->create([
                'name' => 'Test User B',
                'email' => 'test_b@example.com',
                'password' => 'qwerty'
            ]);

        $secondUser->tokens()->create([
            'name' => 'mytoken',
            'token' => hash('sha256', 'qwerty'),
        ]);

        Mod::factory()->count(25)->create([
            'user_id' => $secondUser->id,
            'game_id' => $secondUser->games->first()->id,
        ]);
    }
}
