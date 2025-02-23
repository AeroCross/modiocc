![Mod.io Code Test](header.png "mod.io - Code Test")

Greetings and welcome to the Mod.io Intermediate/Senior PHP Engineer code test!

This repository represents a mini API that isn't quite ready to go live yet. As the senior (or intermediate) engineer on the task it is your responsibility to bring it to a "production ready" state by finishing some endpoints and business logic.

The intention of this API is to host a collection of games, where each of which have their own independent collection of mods.

In this assessment, you will be required to:

1. Thoroughly comprehend the given requirements.
2. Diagnose and rectify any existing issues within the codebase.
3. Fill in any gaps or missing code.
4. Implement any authentication of your choosing.
5. Ensure that all unit tests execute successfully.

> Ultimately, our only aim is to observe your approach to developing a CRUD for two models across two controllers which entails utilising the service layer for business logic and the repository layer for database/model operations.

Bonus points will be awarded for _(please don't feel obligated to do any of these, we appreciate that your time is valuable, these are only listed here in case you have spare time and would like to go above and beyond)_:

* Using Laravel resources in API responses.
* Adding appropriate indexes to table columns to improve performance.
* Implementing validation and displaying validation errors as JSON in the API response.
* Additional security, such as those preventing spam/attack vectors.
* Implementing some form of cache, whether that is simply file-based.
* Code presentation that aligns with industry best practices, including any existing code.
* Comprehensive code documentation following PHPDoc standards.
* Providing a Postman collection or Swagger documentation for the API.
* Ensuring consistency throughout the codebase
* De-duplicating common code into new abstract parent classes or traits
* There are some _really minor_ eastr eggs in this code test, we are interested in if you notice them and are compelled to fix them
* Any other magical goodness you can come up with, surprise us with something awesome if you can and have the time :)

---

# Objective

The primary objective of this test is to elevate the current state of this API to meet the standards expected in a production-ready application. 

The API must be able to consume `application/x-www-form-urlencoded`, or `application/json` but must _always_ respond with `application/json`.

To begin, complete the sprints below.

---

# Sprint 1

This sprint requires you to implement some form of user authentication, it may simply be an API key stored against a user, or you may choose to go all out with `laravel/passport` etc and imeplemnt OAuth2.0

Feel free to create any table(s) you require for the storage of API keys, or OAuth configurations etc.

For simplicity, you are welcome to seed a default API key that you can use in your requests as long as that API key belongs to only one user.

#### Tips

* In certain unit tests, you will need to incorporate your preferred authentication method into the HTTP calls to ensure they pass.

---

# Sprint 2

This sprint requires you to create the CRUD endpoints necessary to manage games.

To be successful you will need to:

#### 1. Complete the migration for the `game` table

Finish the `create_game_table` migration by ensuring it contains the following columns, use the column type that you think is most appropriate:

| Column name  | Description                           |
|--------------|---------------------------------------|
| id           | The ID for the game                   |
| user_id      | The user who created the game         |
| name         | The name of the game                  |
| created_at   | The timestamp the game was created at |
| updated_at   | The timestamp the game was updated at |

#### 2. Complete the `Game` model

Finish defining this empty model with everything it needs, don't forget the relationship it has with the user who created it.

#### 3. Complete the CRUD endpoints

Follow the instructions within the `\App\Http\Controllers\GameController` class.

Ensure the following business rules apply, using a policy where appropriate:

* Only the creator of the game can update or delete it.
* Any user must be able to create a game
* Any user can read/view a game. 

> None of the routes have been registered yet, use the tests to determine the appropriate routes that you need to create to make them work.

#### 4. Complete the browse endpoint

The browse endpoint should return all games in a paginated list.

#### 5. Ensure the tests pass.

All the tests in `./tests/Feature/GameTest.php` must pass.

For _all tests_ to pass you will need to seed some games.

---

# Sprint 3

This sprint requires you to create the CRUD endpoints necessary to manage mods that belong to games.

To be successful you will need to:

#### 1. Complete the migration for the `create_mod` 

Finish the `create_mod_table` migration by ensuring it contains the following columns, use the column type that you think is most appropriate:

| Column name | Description                          |
|-------------|--------------------------------------|
| id          | The ID for the game                  |
| game_id     | The game this mod belongs to         |
| user_id     | The user who created the mod         |
| name        | The name of the mod                  |
| created_at  | The timestamp the mod was created at |
| updated_at  | The timestamp the mod was updated at |

#### 2. Complete the `Mod` model

Finish defining this empty model with everything it needs, don't forget the relationship it has with the game it belongs to, as well as the user who created it.

#### 3. Complete the CRUD endpoints

Follow the instructions within the `App\Http\Controllers\ModController` class.

Ensure the following business rules apply, using one or more policies where appropriate:

* Both the mod and the game must be present in the URL/route, however the mod must belong to the game, otherwise a 404 should be returned. (Finish the existing middleware, and apply it to your route)
* Only the creator of the game or the creator of the mod can update or delete it.
* Any user must be able to create a mod for a game.
* Any user can read/view a game.

> None of the routes have been registered yet, use the tests to determine the appropriate routes that you need to create to make them work.

#### 4. Complete the browse endpoint

The browse endpoint should return all mods for a specific game in a paginated list.

#### 5. Ensure the tests pass.

All the tests in `./tests/Feature/ModTest.php` must pass.

For _all tests_ to pass you will need to seed some mods that belong to already seeded games.

---

# Retro

If you have managed to complete everything so far and all tests are now passing, then congratulations! 🎉 

You can submit your completed code test to us via ZIP, or for extra points yet again; create a public git repository/docker image and send us the link!

We must be able to run the unit tests ourselves, so ensure that is possible :)

Completing the minimal requirements of this code test will cover your ability to understand:

* Models
* Migrations
* Controllers
* Laravel Routing
* Route Model Binding
* Eloquent relationships
* Middleware
* Authentication
* Service layer
* Repository layer
* Access Control/Policies
* Unit Testing

If you were able to also complete all the bonuses mentioned above; they will also cover your ability to understand:

* Laravel resources
* Validation layer
* Security
* Cache layer
* PSR-2/PSR-12 Standards
* API documentation
* PHPDocs
* Consistency
* DRY principal
* Git and/or Docker
