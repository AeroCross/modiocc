<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Game;
use App\Models\Mod;
use App\Models\User;
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
    public function testUpdateSucceedsWhileAuthenticatedUserOwnsTheMod(): void
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

    public function testUpdateSucceedsWhenAuthenticatedUserOwnsTheGameButNotTheMod(): void
    {
        $users = User::factory()->count(2)->create();
        $gameOwner = $users[0];
        $modOwner = $users[1];
        $game = Game::factory()->for($gameOwner)->create();
        $mod = Mod::factory()->for($modOwner)->for($game)->create();

        Sanctum::actingAs($gameOwner);

        $this
            ->patchJson('/api/games/' . $mod->game->id . '/mods/' . $mod->id, [
                'name' => 'Stuff r/rimworld Says (v1.8 compatible)'
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
                'name' => 'Stuff r/rimworld Says (v1.8 compatible)'
            ]);

        $this->assertDatabaseHas('mods', ['name' => 'Stuff r/rimworld Says (v1.8 compatible)']);
    }

    public function testUpdateSucceedsWhenNotChangingModName(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();
        $mod = Mod::factory()->for($user)->for($game)->create(['name' => 'Sanic Skin']);

        Sanctum::actingAs($user);

        $this
            ->patchJson('/api/games/' . $mod->game->id . '/mods/' . $mod->id, [
                'name' => 'Sanic Skin'
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
                'name' => 'Sanic Skin'
            ]);

        $this->assertDatabaseHas('mods', ['name' => 'Sanic Skin']);
    }

    public function testUpdateSucceedsWhenModNameExistsForAnotherGame(): void
    {
        $user = User::factory()->create();
        $games = Game::factory()->count(2)->for($user)->create();

        Mod::factory()->for($user)->for($games[0])->create(['name' => 'gabeN Announcer Pack']);
        Mod::factory()->for($user)->for($games[1])->create(['name' => 'Nicky Minaj Annoucer Pack']);

        $mod = $games[1]->mods->first();

        Sanctum::actingAs($user);

        $this
            ->patchJson('/api/games/' . $games[1]->id . '/mods/' . $mod->id, [
                'name' => 'gabeN Announcer Pack'
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
                'name' => 'gabeN Announcer Pack'
            ]);

        $this->assertEquals(2, Mod::where('name', 'gabeN Announcer Pack')->count());
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

    public function testUpdateFailsWhenAuthenticatedUserDoesNotOwnMod(): void
    {
        $users = User::factory()->count(3)->create();
        $gameOwner = $users[0];
        $modOwner = $users[1];
        $otherUser = $users[2];
        $game = Game::factory()->for($gameOwner)->create();
        $mod = Mod::factory()->for($modOwner)->for($game)->create();

        Sanctum::actingAs($otherUser);

        $this
            ->patchJson('/api/games/' . $game->id . '/mods/' . $mod->id, [
                'name' => 'Path of Melvor'
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('mods', ['name' => 'Path of Melvor']);
    }

    public function testUpdateFailsWhenModDoesNotBelongToGame(): void
    {
        $user = User::factory()->create();
        $games = Game::factory()->count(2)->for($user)->create();

        foreach ($games as $game) {
            Mod::factory()->for($user)->for($game)->create();
        }

        $mods = Mod::all();

        Sanctum::actingAs($user);

        $this
            ->patchJson('/api/games/' . $mods[0]->game->id . '/mods/' . $mods[1]->id, [
                'name' => 'Path of Melvor'
            ])
            ->assertStatus(Response::HTTP_NOT_FOUND);

        $this->assertDatabaseMissing('mods', ['name' => 'Path of Melvor']);
    }

    public function testUpdateFailsWhenModNameExistsInTheSameGame(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();

        Mod::factory()->count(2)->for($user)->for($game)->sequence(
            ['name' => 'No Clip'],
            ['name' => 'Infinite Ammo']
        )->create();

        $mod = Mod::where('name', 'Infinite Ammo')->get()->first();

        $this
            ->patchJson('/api/games/' . $mod->game->id . '/mods/' . $mod->id, [
                'name' => 'No Clip'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseHas('mods', ['name' => 'Infinite Ammo']);
    }

    // DELETE /mods/{modId}
    public function testDeleteSucceedsWhileAuthenticatedUserOwnsMod(): void
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

    public function testDeleteSucceedsWhenAuthenticatedUserOwnsTheGameButNotTheMod(): void
    {
        $users = User::factory()->count(2)->create();
        $gameOwner = $users[0];
        $modOwner = $users[1];
        $game = Game::factory()->for($gameOwner)->create();
        $mod = Mod::factory()->for($modOwner)->for($game)->create();

        Sanctum::actingAs($gameOwner);

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
