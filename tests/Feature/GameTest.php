<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testBrowseSucceeds(): void
    {
        $this
            ->getJson('/api/games/')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'current_page',
                'data' => [[ // as many games as determined by the pagination argument
                    'id',
                    'name',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]]
            ]);
    }

    public function testCreateSucceedsWhileAuthenticated(): void
    {
        $this
            ->asAuthorizedUser()
            ->post('/api/games/', [
                'name' => 'Rogue Knight'
            ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight'
            ]);
    }

    public function testReadSucceeds(): void
    {
        $response = $this->asAuthorizedUser()->post('/api/games/', [
            'name' => 'Rogue Knight'
        ]);

        $this
            ->get('/api/games/' . $response->json('id'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight',
            ]);
    }

    public function testCreateFailsWhileUnauthenticated(): void
    {
        $this
            ->post('/api/games', [
                'name' => 'Rogue Knight'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateSucceedsWhileAuthenticated(): void
    {
        // todo again create the game, include the auth.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ]);

        // todo include the auth
        $this
            ->put('games/' . $response->json('id'), [
                'name' => 'Rogue Knight Remastered'
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight Remastered'
            ]);
    }

    public function testUpdateFailsWhileUnauthenticated(): void
    {
        // todo again create the game, include VALID auth here, just to create the game.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ]);

        // this however should fail with 401 Unauthorized, as expected
        $this
            ->put('games/' . $response->json('id'), [
                'name' => 'Rogue Knight Remastered'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteSucceedsWhileAuthenticated(): void
    {
        // todo again create the game, include the auth.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ]);

        // just to ensure the game actually exists
        $this
            ->get('games/' . $response->json('id'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight'
            ]);

        // todo include the auth
        $this
            ->delete('games/' . $response->json('id'))
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteFailsWhileUnauthenticated(): void
    {
        // todo again create the game, include the auth just so that we have something to attempt to delete.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ]);

        // and just for sanity we make sure it actually got created
        $this
            ->get('games/' . $response->json('id'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id'
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight'
            ]);

        // then we finally attempt to delete it without authentication present
        $this
            ->delete('games/' . $response->json('id'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
