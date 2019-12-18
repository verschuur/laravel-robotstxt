# Dynamic robots.txt ServiceProvider for Laravel

## Installation

    composer require verschuur/laravel-robotstxt

or add the following to your `composer.json`

    {
        "require": {
            "verschuur/laravel-robotstxt": "^1.0"
        }
    }

After updating Composer, add the ServiceProvider to the providers array in `config/app.php`.

     Verschuur\Laravel\RobotsTxt\Providers\RobotsTxtProvider::class

## Usage

### Basic usage

This package adds a route `/robots.txt` to your application. __Remember to remove the physical `robots.txt` file from your `/public` dir or else it will take precedence over Laravel's route and this package will not work.__

By default, the `production` environment will show

    User-agent: *
    Disallow:

while every other environment will show

    User-agent: *
    Disallow: /

This will allow a basic install to allow all robots on a production install, while disallowing robots on every other environment (such as staging, acceptation etc).

### Custom settings

If you need custom settings, publish the configuration file

    php artisan vendor:publish --provider="Verschuur\Laravel\RobotsTxt\RobotsTxtProvider"

This will copy `robots-txt.php` to your app's `config` folder, allowing you fine-grained control over which paths to disallow for which environment and robot. In this file you will find the following array structure

    'paths' => [
        'production' => [
            '*' => [
                ''
            ]
        ]
    ]

### Examples

Allow all paths for all robots on production, and disallow everything for every robot in staging

    'paths' => [
        'production' => [
            '*' => [
                ''
            ]
        ],
        'staging' => [
            '*' => [
                '/'
            ]
        ]
    ]

Allow all paths for all robot __bender__ on production, but disallow `/admin` and `/images` on production for robot __flexo__

    'paths' => [
        'production' => [
            'bender' => [
                ''
            ],
            'flexo' => [
                '/admin',
                '/images'
            ]
        ],
    ]

## Compatiblility

This package is compatible with Laravel 5.8

## Testing

PHPUnit test cases are provided in `/tests`. Run the tests through `composer run test` or `vendor/bin/phpunit --configuration phpunit.xml`.

## Link

<https://developers.google.com/search/reference/robots_txt>
