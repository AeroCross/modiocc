<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Game;
use App\Models\Mod;
use App\Models\User;
use App\Services\ModService;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ModTest extends TestCase
{
    use RefreshDatabase;

    // GET /mods
    public function testBrowseSucceeds(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();
        $mods = Mod::factory()->count(30)->for($user)->for($game)->create();

        $this
            ->getJson('/api/games/' . $mods->first()->game_id . '/mods?page=2')
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
                    'per_page',
                    'total',
                ]
            ])->assertJsonFragment([
                'current_page' => 2,
                'total' => 30
            ]);
    }

    public function testBrowseFailsWhenGameDoesNotExist()
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();

        $this
            ->getJson('/api/games/' . $game->id + 1 . '/mods')
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    // GET /mods/{modId}
    public function testReadSucceeds(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();
        $mod = Mod::factory()->for($user)->for($game)->create();

        $this
            ->getJson('/api/games/' . $mod->game->id . '/mods/' . $mod->id)
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

    public function testReadFailsWhenGameDoesNotExist(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();
        $mod = Mod::factory()->for($user)->for($game)->create();

        $this
            ->getJson('/api/games/' . $mod->game->id + 1 . '/mods/' . $mod->id)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testReadFailsWhenModDoesNotExist(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();
        $mod = Mod::factory()->for($user)->for($game)->create();

        $this
            ->getJson('/api/games/' . $mod->game->id + 1 . '/mods/' . $mod->id + 1)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testReadFailsWhenModDoesNotBelongToGame(): void
    {
        $user = User::factory();
        $games = Game::factory()->count(2)->for($user)->create();

        foreach ($games as $game) {
            Mod::factory()->for($game)->create();
        }

        $this
            ->getJson('/api/games/' . $games[0]->id . '/mods/' . $games[1]->mods->first()->id)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    // POST /mods
    public function testCreateSucceedsWhileAuthenticated(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();
        $mod = Mod::factory()->for($user)->for($game)->create();

        Sanctum::actingAs($user);

        $this
            ->postJson('/api/games/' . $mod->game->id . '/mods', [
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
                'game_id' => $mod->game->id,
                'user_id' => $mod->user->id,
            ]);

        $this->assertDatabaseHas('mods', ['name' => 'Lightsaber']);
    }

    public function testCreateSucceedsWhenModNameExistsForAnotherGame(): void
    {
        $user = User::factory()->create();
        $games = Game::factory()->count(2)->for($user)->create();

        Mod::factory()->for($games[0])->for($user)->create(['name' => 'Faster Walk Speed']);

        Sanctum::actingAs($user);

        $this
            ->postJson('/api/games/' . $games[1]->id . '/mods', [
                'name' => 'Faster Walk Speed'
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
                'name' => 'Faster Walk Speed',
                'game_id' => $games[1]->id,
                'user_id' => $user->id,
            ]);

        $this->assertEquals(2, Mod::where('name', 'Faster Walk Speed')->count());
    }

    public function testCreateSucceedsWhenAuthenticatedUserDoesNotOwnGame(): void
    {
        $users = User::factory()->count(2)->create();
        $owner = $users[0];
        $nonOwner = $users[1];
        $game = Game::factory()->for($owner)->create();

        Sanctum::actingAs($nonOwner);

        $this
            ->postJson('/api/games/' . $game->id . '/mods', [
                'name' => 'Satisfactorio'
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
                'name' => 'Satisfactorio',
                'game_id' => $game->id,
                'user_id' => $nonOwner->id,
            ]);

        $this->assertDatabaseHas('mods', ['name' => 'Satisfactorio']);
    }

    public function testCreateFailsWhileUnauthenticated(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();

        $this
            ->postJson('/api/games/' . $game->id . '/mods', [
                'name' => 'Lightsaber'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseMissing('mods', [
            'name' => 'Lightsaber',
        ]);
    }

    public function testCreateFailsWhenModNameExistsForSameGame(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();

        Mod::factory()->count(2)->sequence(
            ['name' => 'Krastorio 2'],
            ['name' => 'Nullius']
        )->for($user)->for($game)->create();

        Sanctum::actingAs($user);

        $this
            ->postJson('/api/games/' . $game->id . '/mods', [
                'name' => 'Krastorio 2'
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertEquals(1, Mod::where('name', 'Krastorio 2')->count());
    }

    // PATCH /mods/{modId}
    public function testUpdateSucceedsWhileAuthenticated(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();
        $mod = Mod::factory()->for($user)->for($game)->create();

        Sanctum::actingAs($mod->user);

        $this
            ->patchJson('/api/games/' . $mod->game->id . '/mods/' . $mod->id, [
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

        $this->assertDatabaseHas('mods', ['name' => 'Lightsabers (Full set)']);
    }

    public function testUpdateFailsWhileUnauthenticated(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();
        $mod = Mod::factory()->for($user)->for($game)->create([
            'name' => 'Various Katanas'
        ]);

        $this
            ->patchJson('/api/games/' . $mod->game->id . '/mods/' . $mod->id, [
                'name' => 'Lightsabers (Full set)'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseHas('mods', ['name' => 'Various Katanas']);
    }

    // DELETE /mods/{modId}
    public function testDeleteSucceedsWhileAuthenticated(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();
        $mod = Mod::factory()->for($user)->for($game)->create();

        Sanctum::actingAs($mod->user);

        $this
            ->deleteJson('/api/games/' . $mod->game->id . '/mods/' . $mod->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertModelMissing($mod);
    }

    public function testDeleteFailsWhileUnauthenticated(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();
        $mod = Mod::factory()->for($user)->for($game)->create();

        $this
            ->deleteJson('/api/games/' . $mod->game->id . '/mods/' . $mod->id)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertModelExists($mod);
    }
}
