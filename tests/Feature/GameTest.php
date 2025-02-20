<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class GameTest extends TestCase
{
    public function testBrowseSucceeds() : void
    {
        // todo update this test to assert that a paginated response was given
        //  in order for this test to pass, you will need to seed at least 1 game
        $this
            ->get('games')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id',
                'created_at',
                'updated_at'
            ]);
    }

    public function testCreateSucceedsWhileAuthenticated() : void
    {
        // todo this endpoint must be secured by user authentication, modify the post call
        //   below to include the required header or URL parameter to achieve that
        $this
            ->post('games', [
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

    public function testReadSucceeds() : void
    {
        // todo create the game that we are going to view, adding the required authentication
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ]);

        $this
            ->get('games/'.$response->json('id'))
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

    public function testCreateFailsWhileUnauthenticated() : void
    {
        $this
            ->post('games', [
                'name' => 'Rogue Knight'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateSucceedsWhileAuthenticated() : void
    {
        // todo again create the game, include the auth.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ]);

        // todo include the auth
        $this
            ->put('games/'.$response->json('id'), [
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

    public function testUpdateFailsWhileUnauthenticated() : void
    {
        // todo again create the game, include VALID auth here, just to create the game.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ]);

        // this however should fail with 401 Unauthorized, as expected
        $this
            ->put('games/'.$response->json('id'), [
                'name' => 'Rogue Knight Remastered'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteSucceedsWhileAuthenticated() : void
    {
        // todo again create the game, include the auth.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ]);

        // just to ensure the game actually exists
        $this
            ->get('games/'.$response->json('id'))
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
            ->delete('games/'.$response->json('id'))
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteFailsWhileUnauthenticated() : void
    {
        // todo again create the game, include the auth just so that we have something to attempt to delete.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ]);

        // and just for sanity we make sure it actually got created
        $this
            ->get('games/'.$response->json('id'))
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
            ->delete('games/'.$response->json('id'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
