<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\User;
use \App\Models\Game;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // These are meant to be users that allow the test suite to run.
        $firstUser = User::factory()->has(Game::factory()->count(3))
            ->create([
                'name' => 'Test User A',
                'email' => 'test_a@example.com',
                'password' => 'asdf'
            ]);

        $firstUser->tokens()->create([
            'name' => 'modio',
            'token' => hash('sha256', 'asdf'),
        ]);

        $secondUser = User::factory()->has(Game::factory()->count(5))
            ->create([
                'name' => 'Test User B',
                'email' => 'test_b@example.com',
                'password' => 'qwerty'
            ]);

        $secondUser->tokens()->create([
            'name' => 'mytoken',
            'token' => hash('sha256', 'qwerty'),
        ]);
    }
}
