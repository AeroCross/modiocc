![Mod.io Code Test](header.png "mod.io - Code Test")

Thanks for taking the time to check this out!

## Requirements

This code challenge was developed and tested with the following:

- MacOS Sequoia 15.3
- PHP 8.4.4
- MySQL 9.2
- Composer 2.8.5
- git 2.44

To install dependencies:

```bash
# git comes installed with MacOS
brew install mysql php composer 
```

## Running locally

This assumes a fresh installation of PHP (which should be in your `$PATH`), `composer` and MySQL.

The default MySQL installation from Brew comes configured with passwordless `root` and **does not start by default as a service**.

Please makes sure you update the commands depending on your environment.

```bash
# Clone / unzip the repository into your computer and cd into it
git clone https://github.com/AeroCross/modiocc.git
cd modiocc

# Create the development database. Make sure MySQL is running.
mysql -u root -e 'CREATE DATABASE laravel;'

# Install dependencies and setup the environment
composer install
cp .env.example .env
php artisan key:generate
php artisan config:cache
php artisan migrate && php artisan db:seed

# Good to go!
php artisan serve
```

The accompanying [Postman Collection](/postman.json) assumes that the server is running with a freshly seeded database
on `localhost:8000`.

## Testing

```bash
# Follow the instructions for the local environment, then...
# Create the testing database
mysql -u root -e 'CREATE DATABASE laravel_test;'

# Setup the environment
php artisan migrate --database='mysql_test'

# Also good to go!
php artisan test
```

## Additional notes

- The original README.md with all the instructions is located in [REQUIREMENTS.md](/REQUIREMENTS.md).
- I've explailned some (very much not all!) of my thoughts and decisions in [DESIGN.md](/DESIGN.md). There are more, but I don't want to spoil all of them!
