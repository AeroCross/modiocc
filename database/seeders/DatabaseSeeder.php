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
        // \App\Models\User::factory(10)->create();

        $firstUser = User::factory()->has(Game::factory()->count(3))
            ->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'asdf'
            ]);

        $firstUser->tokens()->create([
            'name' => 'modio',
            'token' => hash('sha256', 'asdf'),
        ]);
    }
}
