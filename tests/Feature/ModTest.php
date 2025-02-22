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
        // todo again create the game, include the auth.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ])->assertStatus(Response::HTTP_CREATED);

        // todo create the mod, include the auth.
        $response = $this->post('games/' . $response->json('id') . '/mods', [
            'name' => 'Lightsaber'
        ])->assertStatus(Response::HTTP_CREATED);

        // todo update the game, include the auth.
        $this
            ->put('games/' . $mod->id . '/mods/' . $game->id, [
                'name' => 'Lightsabers (Full set)'
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'game_id',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Lightsabers (Full set)'
                // todo assert game is valid
                // todo assert user is valid
            ]);
    }

    public function testUpdateFailsWhileUnauthenticated(): void
    {
        // todo again create the game, include VALID auth here, just to create the game successfully.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ]);

        // this however should fail with 401 Unauthorized, as expected
        $this
            ->put('games/' . $response->json('id') . '/mods/' . $mod->id, [
                'name' => 'Lightsabers (Full set)'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteSucceedsWhileAuthenticated(): void
    {
        // todo again create the game, include the auth just so that we have something to attempt to delete.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ]);

        $gameId = $response->json('id');

        // todo create the mod, include the auth.
        $response = $this->post('games/' . $gameId . '/mods', [
            'name' => 'Lightsaber'
        ])->assertStatus(Response::HTTP_CREATED);

        // and just for sanity we make sure it actually got created
        $this
            ->get('games/' . $gameId . '/mods/' . $response->json('id'))
            ->assertStatus(Response::HTTP_OK);

        // then we finally attempt to delete it without authentication present
        $this
            ->delete('games/' . $gameId . '/mods/' . $response->json('id'))
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteFailsWhileUnauthenticated(): void
    {
        // todo again create the game, include the auth just so that we have something to attempt to delete.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ]);

        $gameId = $response->json('id');

        // todo create the mod, include the auth.
        $response = $this->post('games/' . $gameId . '/mods', [
            'name' => 'Lightsaber'
        ])->assertStatus(Response::HTTP_CREATED);

        // and just for sanity we make sure it actually got created
        $this
            ->get('games/' . $gameId . '/mods/' . $response->json('id'))
            ->assertStatus(Response::HTTP_OK);

        // then we finally attempt to delete it without authentication present
        $this
            ->delete('games/' . $gameId . '/mods/' . $response->json('id'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
