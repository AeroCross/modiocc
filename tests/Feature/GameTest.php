<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    public function testBrowseSucceeds(): void
    {
        $this
            ->getJson('/api/games/')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [[ // as many games as determined by the pagination argument
                    'id',
                    'name',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]],
                'meta' => [
                    'current_page'
                ]
            ]);
    }

    public function testCreateSucceedsWhileAuthenticated(): void
    {
        $this
            ->asAuthorizedUser()
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
        $this
            ->getJson('/api/games/1')
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
        $this
            ->asAuthorizedUser()
            ->patchJson('/api/games/1', [
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
        $this
            ->asAuthorizedUser('b') // Test User B has access to games ID 4-8
            ->patchJson('/api/games/1', [
                'name' => 'Rogue Knight Remastered'
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testUpdateFailsWhileUnauthenticated(): void
    {
        $this
            ->patchJson('/api/games/1', [
                'name' => 'Rogue Knight Remastered'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteSucceedsWhileAuthenticated(): void
    {
        $this
            ->asAuthorizedUser()
            ->deleteJson('/api/games/1')
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteFailsWhileUnauthenticated(): void
    {
        $this
            ->deleteJson('/api/games/1')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteFailsWhileAuthenticatedWithDifferentOwner(): void
    {
        $this
            ->asAuthorizedUser('b')
            ->deleteJson('/api/games/1')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
