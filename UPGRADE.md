<h1>robots.txt upgrade guide 🛠</h1>

- [Upgrading from v1 to v2](#upgrading-from-v1-to-v2)
  - [Compatibilty](#compatibilty)
  - [Service provider registration](#service-provider-registration)
  - [Namespace](#namespace)
  - [Problems during installation](#problems-during-installation)
  - [Path structure](#path-structure)
    - [Example](#example)
  
# Upgrading from v1 to v2

## Compatibilty

Due to a lot of refactoring, this version is **not** compatible with v1.

## Service provider registration

This package now supports Laravel's package auto-discovery. You can remove the line:

`Gverschuur\RobotsTxt\RobotsTxtProvider::class` from your service providers in the `config/app.php` file.

## Namespace

- If you included any files from the package yourself (by extending etc), change the namespace from `Gverschuur\RobotsTxt` to `Verschuur\RobotsTxt`.

## Problems during installation

Due to the renaming of the namespace, there might be an error during installing due to conflicts. If this happens, first revote the provider from the app config file, then dump the autoloader and finally rerun the installation.

## Path structure

This version has a change to how the paths are defined in the config file. The v1 version was as follows:

_paths -> {environment name} -> {robot name} -> {disallowed entries}_

e.g.: _paths -> production -> *robot name* -> Disallow all_

This has been changed in v2. The order is now:

_environments -> {environment name} -> paths -> {robot name} -> {disallowd/allow entries}_

e.g.: _environments -> production -> paths -> *{robot name}* -> Disallow all_

### Example

For example, let's say your configuration is as follows:

```php
'paths' => [
    'production' => [
        'bender' => [
            ''
        ],
        'flexo' => [
            'images'
        ]
    ],
    'staging' => [
        '*' => [
            '/'
        ]
    ]
]
```

Then the new configuration would be:

```php
'environments' => [
    'production' => [
        'paths' => [
            'bender' => [
                'disallow' => [
                    ''
                ],
                'allow' => []
            ],
            'flexo' => [
                'disallow' => [
                    'images'
                ],
                'allow' => []
            ]
        ]
    ],
    'staging' => [
        'paths' => [
            '*' => [
                'disallow' => [
                    '/'
                ],
            ]
        ]
    ]
]
```