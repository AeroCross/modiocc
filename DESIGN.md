# Design decisions and assumptions

There are many things that I have changed in both the tests and in the app itself. I would like to take a moment
to go through a few of them that I felt would be valuable to provide additional context.

Generally speaking, I treated the files that came with the challenge as if they were a requirements document to fulfil
instead of a blank canvas, until they hit a point where I believed it would be detrimental to follow them to the letter.

Many of the assumptions and eventually decisions I made stem from this.

### The route root: `/api/games`

The tests indicated that the URL root that the requests should be going to was `<host>/games`. Considering that this is
an API I felt it was more appropriate to have the root of it to be at the very least `/api`. This would allow further
development in the future where a web UI could use the root.

### `{data: [{}]}` as the top level JSON response object

The tests were indicating that the JSON structure of the response should have been an array of objects. The 
[JSON API Specification] establishes that there should be at least 2 top level properties in a JSON response object: 
`data` and `links` (where appropriate). I deviated from the original specification in the tests due to this, and I
strived to be consistent across the application.

[JSON API Specification]: https://jsonapi.org/format/#fetching-resources-responses

### API Resource Objects

I'm not really happy with my implementation of API Resource objects, and what's most frustrating of all is that I wasn't
able to figure out exactly why my implementation wasn't working [as per the documentation](https://laravel.com/docs/11.x/eloquent-resources#resource-collections). As far as I could see, I _should_ have been able to return cleanly the
resources from my controllers without having to call the `response()` method. This was also causing issues
with the Controller interfaces (which I tried my best to stick with, then had to change them due to lack of time for the
purposes of rapid development) which is why I was going back and forth between returning `JsonResponse` and `Game|ModResponse`.

Part of me wonders if this is due to the upgrade from Laravel 10.x to 11. Part of me believes this is unfamiliarity with
current modern practices. This is one of the many things I would have loved to have a team to be able to chat with
to come to a reasonable solution that is maintainable.

### Comments throughout the application

I went out of my way to explain certain decisions in-context within the code. I would usually not do this amount of depth
in a production system, unless it is discussed with my immediate team beforehand or if I am trying to leave important
context behind.

This includes TODO in this submission. I've left some TODOs in an effort to communicate "hey, I'm not happy with this,
and I'd like to change it". I encourage you to go around and have a look at those :)

### Expanded test suite, missing other tests

I focused heavily on updating the feature tests to ensure there's adequate coverage of most use cases and edge cases
that I could think of, foregoing unit tests in the process for the benefit of speed.

I particularly like using unit tests but since there was a reasonable testing structure already set in the exercise, I
decided to test most of the code via those feature tests. Things like the middlewares I wrote, services, etc. would have
been perfect candidates for that, but at the same time there was a bit of redundant work there that could be technically
covered by the feature tests.

### PATCH vs. PUT

The tests indicated that requests to update resources should be using the PUT verb. However, none of the update
operations avaialble in the system require (or expect?) the resubmission of the entirety of the resource for it to be
updated. In the precise case of this challenge, it is expected to only submit the name of the resource to update,
but nothing like timestamps or IDs. Therefore, it makes more sense to use PATCH, as it only deals with partial updates
of a resource that already exists.

### Seeding vs. Per-test setup

If you look at my Git history (which I've left a ton of context in for posterity if you so desire to look into it)
you will notice that my tests were a lot leaner on the setup side. This is due to a "seed-first" approach. In this
approach, I use the same seeded data used for local development in tests, then use the `RefreshDatabases` trait with
`seed = true`. This means that if you know what the database already contains (or create helper methods in something like
the parent `TestCase` class), you need to do way less setup per test. This, however, requires having loaded a bunch of
context.

I see value in that but also in the "co-located" approach, where you have the setup next to the thing you're asserting.
This makes it really easy at a glance to reason about what the test is doing and what data it is operating upon.

Both approaches have merit, and I do not particularly feel passionate about either. At the end of the day, I decided to
go with the "high co-location" approach to make loading context a lot easier for the reader — you, and circumvent problems like unique value generation when using Factories and Faker. 

### .env files

Due to the nature of using Personal Access Tokens (PAT) via Laravel Sanctum, it was important to have seeded tokens.

These development tokens in the .env files would, in other cases, not have made it into git, and would have required
to be manually set per-environment and left out of the seed just in case, but to lessen the load on whoever is evaluating
this challenge I decided to leave it there so setup is more smooth. I could also write a setup script, but there's no time!

### Validation: HTTP 422 vs. 403

Laravel's default validation HTTP response code is 422 (Unprocessable entity) when it also does database checks.
If you do a unique database lookup and the request cannot continue due to not being able to insert
a duplicate record, previous experience indicates that it makes sense to return a 403 (Forbidden), and we may be able
to explain to the user why, as established in [RFC 9110](https://httpwg.org/specs/rfc9110.html#status.403).

This is another example of something that I would discuss with the broader team to reach an agreement and disseminate
the consensus, potentially through an RFC / ADR.

### There's more!

I have many opinions and thoughts about how this went but I want to leave stuff out for the interview. Please ask about
anything and I'd be happy to oblige :) 
