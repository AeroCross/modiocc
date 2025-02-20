<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Game;
use App\Models\Mod;
use Illuminate\Http\Response;
use Tests\TestCase;

class ModTest extends TestCase
{
    public function testBrowseSucceeds() : void
    {
        // todo update this test to assert that a paginated response was given.
        //  in order for this test to pass, you will need to seed at least 1 game
        //  and 1 mod
        $game = Game::inRandomOrder()->first();
        $mod = Game::query()->where('game', '=', $game->id)->first();

        $this
            ->get('games/'.$game->id.'/mods')
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
                'id' => $mod->id
                // todo assert game is valid
                // todo assert user is valid
            ]);
    }

    public function testCreateSucceedsWhileAuthenticated() : void
    {
        $game = Game::inRandomOrder()->first();

        // todo this endpoint must be secured by user authentication, modify the post call
        //   below to include the required header or URL parameter to achieve that
        $this
            ->post('games/'.$game->id.'/mods', [
                'name' => 'Lightsaber'
            ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'id',
                'name',
                'game_id',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Lightsaber'
                // todo assert game is valid
                // todo assert user is valid
            ]);
    }

    public function testReadSucceeds() : void
    {
        // todo create the game that we are going to add a mod to, adding the required authentication
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ])->assertStatus(Response::HTTP_CREATED);

        // todo create the mod against the game we created above, add your auth
        $response = $this->post('games/'.$response->json('id').'/mods', [
            'name' => 'Lightsaber'
        ])->assertStatus(Response::HTTP_CREATED);

        // view the mod
        $this
            ->get('games/'.$response->json('id').'/mods/'.$response->json('id'))
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
                'name' => 'Lightsaber',
                // todo assert game is valid
                // todo assert user is valid
            ]);


    }

    public function testCreateFailsWhileUnauthenticated() : void
    {
        $game = Game::inRandomOrder()->first();

        $this
            ->post('games/'.$game->id.'/mods', [
                'name' => 'Lightsaber'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateSucceedsWhileAuthenticated() : void
    {
        // todo again create the game, include the auth.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ])->assertStatus(Response::HTTP_CREATED);

        // todo create the mod, include the auth.
        $response = $this->post('games/'.$response->json('id').'/mods', [
            'name' => 'Lightsaber'
        ])->assertStatus(Response::HTTP_CREATED);

        // todo update the game, include the auth.
        $this
            ->put('games/'.$mod->id.'/mods/'.$game->id, [
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

    public function testUpdateFailsWhileUnauthenticated() : void
    {
        // todo again create the game, include VALID auth here, just to create the game successfully.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ]);

        // this however should fail with 401 Unauthorized, as expected
        $this
            ->put('games/'.$response->json('id').'/mods/'.$mod->id, [
                'name' => 'Lightsabers (Full set)'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteSucceedsWhileAuthenticated() : void
    {
        // todo again create the game, include the auth just so that we have something to attempt to delete.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ]);

        $gameId = $response->json('id');

        // todo create the mod, include the auth.
        $response = $this->post('games/'.$gameId.'/mods', [
            'name' => 'Lightsaber'
        ])->assertStatus(Response::HTTP_CREATED);

        // and just for sanity we make sure it actually got created
        $this
            ->get('games/'.$gameId.'/mods/'.$response->json('id'))
            ->assertStatus(Response::HTTP_OK);

        // then we finally attempt to delete it without authentication present
        $this
            ->delete('games/'.$gameId.'/mods/'.$response->json('id'))
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteFailsWhileUnauthenticated() : void
    {
        // todo again create the game, include the auth just so that we have something to attempt to delete.
        $response = $this->post('games', [
            'name' => 'Rogue Knight'
        ]);

        $gameId = $response->json('id');

        // todo create the mod, include the auth.
        $response = $this->post('games/'.$gameId.'/mods', [
            'name' => 'Lightsaber'
        ])->assertStatus(Response::HTTP_CREATED);

        // and just for sanity we make sure it actually got created
        $this
            ->get('games/'.$gameId.'/mods/'.$response->json('id'))
            ->assertStatus(Response::HTTP_OK);

        // then we finally attempt to delete it without authentication present
        $this
            ->delete('games/'.$gameId.'/mods/'.$response->json('id'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
