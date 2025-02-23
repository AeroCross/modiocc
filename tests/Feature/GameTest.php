<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    public function testBrowseSucceeds(): void
    {
        $user = User::factory()->create();
        Game::factory()->count(30)->for($user)->create();

        $this
            ->getJson('/api/games?page=2')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'name',
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

    public function testCreateSucceedsWhileAuthenticated(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this
            ->postJson('/api/games/', [
                'name' => 'Rogue Knight'
            ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight'
            ]);
    }

    public function testReadSucceeds(): void
    {
        $game = Game::factory()->for(User::factory())->create();

        $this
            ->getJson('/api/games/' . $game->id)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function testReadNotFound(): void
    {
        $game = Game::factory()->for(User::factory())->create();

        $this
            ->getJson('/api/games/' . $game->id + 1)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }


    public function testCreateFailsWhileUnauthenticated(): void
    {
        $this
            ->postJson('/api/games', [
                'name' => 'Rogue Knight'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateSucceedsWhileAuthenticated(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();

        Sanctum::actingAs($user);

        $this
            ->patchJson('/api/games/' . $game->id, [
                'name' => 'Rogue Knight Remastered'
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight Remastered'
            ]);
    }

    public function testUpdateFailsWhileAuthenticatedWithDifferentOwner(): void
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $game = Game::factory()->for($owner)->create();

        Sanctum::actingAs($user);

        $this
            ->patchJson('/api/games/' . $game->id, [
                'name' => 'Rogue Knight Remastered'
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testUpdateFailsWhileUnauthenticated(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();

        $this
            ->patchJson('/api/games/' . $game->id, [
                'name' => 'Rogue Knight Remastered'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateSucceedsWhenUpdatingWithSameName(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create([
            'name' => 'Rogue Knight Remastered'
        ]);

        Sanctum::actingAs($user);

        $this
            ->patchJson('/api/games/' . $game->id, [
                'name' => 'Rogue Knight Remastered'
            ])
            ->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateFailsWhenNameAlreadyExists(): void
    {
        $user = User::factory()->create();

        Game::factory()->for($user)->create([
            'name' => 'Call of Duty: Modern Warfare'
        ]);

        $game = Game::factory()->for($user)->create();

        Sanctum::actingAs($user);

        $this
            ->patchJson('/api/games/' . $game->id, [
                'name' => 'Call of Duty: Modern Warfare'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteSucceedsWhileAuthenticated(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();

        Sanctum::actingAs($user);

        $this
            ->deleteJson('/api/games/' . $game->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteFailsWhileUnauthenticated(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();

        $this
            ->deleteJson('/api/games/' . $game->id)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteFailsWhileAuthenticatedWithDifferentOwner(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $game = Game::factory()->for($owner)->create();

        Sanctum::actingAs($user);

        $this
            ->deleteJson('/api/games/' . $game->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
