<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Game;
use App\Models\Mod;
use Illuminate\Http\Response;
use Tests\TestCase;

class ModTest extends TestCase
{
    use RefreshDatabase;

    public function testBrowseSucceeds(): void
    {
        $mod = Mod::first();

        $this
            ->getJson('/api/games/' . $mod->game_id . '/mods')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'name',
                    'game_id',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]],
                'meta' => [
                    'current_page',
                    'last_page'
                ]
            ]);
    }

    public function testCreateSucceedsWhileAuthenticated(): void
    {
        $this
            ->asAuthorizedUser()
            ->postJson('/api/games/1/mods', [
                'name' => 'Lightsaber'
            ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'game_id',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJsonFragment([
                'name' => 'Lightsaber',
                'game_id' => 1,
                'user_id' => 1,
            ]);
    }

    public function testReadSucceeds(): void
    {
        $this
            ->getJson('/api/games/1/mods/1')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'game_id',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function testReadFailsWhenModDoesNotBelongToGame(): void
    {
        $this
            ->getJson('/api/games/1/mods/13') // Mod ID 12 belongs to Game ID 2
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCreateFailsWhileUnauthenticated(): void
    {
        $this
            ->postJson('/api/games/1/mods', [
                'name' => 'Lightsaber'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateSucceedsWhileAuthenticated(): void
    {
        $this
            ->asAuthorizedUser()
            ->patchJson('/api/games/1/mods/1', [
                'name' => 'Lightsabers (Full set)'
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'game_id',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJsonFragment([
                'name' => 'Lightsabers (Full set)'
            ]);
    }

    public function testUpdateFailsWhileUnauthenticated(): void
    {
        $this
            ->patchJson('/api/games/1/mods/1', [
                'name' => 'Lightsabers (Full set)'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteSucceedsWhileAuthenticated(): void
    {
        $this
            ->asAuthorizedUser()
            ->deleteJson('/api/games/1/mods/2')
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteFailsWhileUnauthenticated(): void
    {
        $this
            ->delete('/api/games/1/mods/2')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
