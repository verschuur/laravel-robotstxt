![Run tests](https://github.com/verschuur/laravel-robotstxt/workflows/Run%20tests/badge.svg?branch=master) ![Code Climate issues](https://img.shields.io/codeclimate/issues/verschuur/laravel-robotstxt.svg?style=flat-square) ![Code Climate maintainability](https://img.shields.io/codeclimate/maintainability/verschuur/laravel-robotstxt.svg?style=flat-square) ![Scrutinizer](https://img.shields.io/scrutinizer/g/verschuur/laravel-robotstxt.svg?style=flat-square)

<h1>Dynamic robots.txt ServiceProvider for Laravel 🤖</h1>

- [Installation](#installation)
  - [Composer](#composer)
  - [Manual](#manual)
  - [Service provider registration](#service-provider-registration)
- [Usage](#usage)
  - [Basic usage](#basic-usage)
  - [Custom settings](#custom-settings)
  - [Examples](#examples)
    - [Allow directive](#allow-directive)
  - [Sitemaps](#sitemaps)
    - [The standard production configuration](#the-standard-production-configuration)
    - [Adding multiple sitemaps](#adding-multiple-sitemaps)
- [Compatiblility](#compatiblility)
- [Testing](#testing)
- [robots.txt reference](#robotstxt-reference)

# Installation

## Composer

```bash
composer require verschuur/laravel-robotstxt
```

## Manual

Add the following to your `composer.json` and then run `composer install`.

```php
{
    "require": {
        "verschuur/laravel-robotstxt": "^3.0"
    }
}
```

## Service provider registration

This package supports Laravel's service provider autodiscovery so that's it. If you wish to register the package manually, add the ServiceProvider to the providers array in `config/app.php`.

```php
Verschuur\Laravel\RobotsTxt\Providers\RobotsTxtProvider::class
```

# Usage

## Basic usage

This package adds a `/robots.txt` route to your application. Remember to remove the physical `robots.txt` file from your `/public` dir or else it will take precedence over Laravel's route and this package will not work.

By default, the `production` environment will show

```txt
User-agent: *
Disallow:
```

while every other environment will show

```txt
User-agent: *
Disallow: /
```

This will allow the default install to allow all robots on a production environment, while disallowing robots on every other environment. If you do not define an environment in this file, the default will always be to disallow all bots for all paths.

## Custom settings

If you need custom sitemap entries, publish the configuration file

```bash
php artisan vendor:publish --provider="Verschuur\Laravel\RobotsTxt\RobotsTxtProvider"
```

This will copy the `robots-txt.php` config file to your app's `config` folder. In this file you will find the following array structure

```php
'environments' => [
    '{environment name}' => [
        'paths' => [
            '{robot name}' => [
                'disallow' => [
                    ''
                ],
                'allow' => []
            ],
        ]
    ]
]
```

In which:

- `{environment name}`: the enviroment for which to define the paths.
- `{robot name}`: the robot for which to define the paths.
- `disallow`: all entries which will be used by the `disallow` directive.
- `allow`: all entries which will be used by the `allow` directive.

By default, the environment name is set to `production` with a robot name of `*` and a disallow entry consisting of an empty string. This will allow all bots to access all paths on the production environment.

## Examples

For brevity, the `environment` array key will be disregarded in these examples.

Allow all paths for all robots on production, and disallow all paths for every robot in staging.

```php
'production' => [
    'paths' => [
        '*' => [
            'disallow' => [
                ''
            ]
        ]
    ]
],
'staging' => [
    'paths' => [
        '*' => [
            'disallow' => [
                '/'
            ]
        ]
    ]
]
```

Allow all paths for all robot _bender_ on production, but disallow `/admin` and `/images` on production for robot _flexo_

```php
'production' => [
    'paths' => [
        'bender' => [
            'disallow' => [
                ''
            ]
        ],
        'flexo' => [
            'disallow' => [
                '/admin',
                '/images'
            ]
        ]
    ]
],
```

### Allow directive

Besides the more standard `disallow` directive, the `allow` directive is also supported.

Allow a path, but disallow sub paths:

```php
'production' => [
    'paths' => [
        '*' => [
            'disallow' => [
                '/foo/bar'
            ],
            'allow' => [
                '/foo'
            ]
        ]
    ]
],
```

When the file is rendered, the `disallow` directives will always be placed before the `allow` directives.

If you don't need one or the other directive, and you wish to keep the configuration file clean, you can simply remove the entire key from the entire array.

## Sitemaps

This package also allows to add sitemaps to the robots file. By default, the production environment will add a sitemap.xml entry to the file. You can remove this default entry from the `sitemaps` array if you don't need it.

Because sitemaps always need to an absolute url, they are automatically wrapped using [Laravel's url() helper function](https://laravel.com/docs/7.x/helpers#method-url). The sitemap entries in the config file should be relative to the webroot.

### The standard production configuration

```php
'environments' => [
    'production' => [
        'sitemaps' => [
            'sitemap.xml'
        ]
    ]
]
```

### Adding multiple sitemaps

```php
'environments' => [
    'production' => [
        'sitemaps' => [
            'sitemap-articles.xml',
            'sitemap-products.xml',
            'sitemap-etcetera.xml'
        ]
    ]
]
```

# Compatiblility

This package is compatible with Laravel 5.6 and up, 6 and 7. For a complete overview of supported Laravel and PHP versions, please refer to the ['Run test' workflow](https://github.com/verschuur/laravel-robotstxt/actions).

# Testing

PHPUnit test cases are provided in `/tests`. Run the tests through `composer run test` or `vendor/bin/phpunit --configuration phpunit.xml`.

# robots.txt reference

The following reference was while creating this package:

<https://developers.google.com/search/reference/robots_txt>
