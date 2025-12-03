<div align="center">

<br>


<img src="docs/static/factotumcms-logo.svg" alt="Logo" width="150" />
<br>

## Factotum CMS - Base Module

`php` `laravel` `base-module`

maintained with ❤️ by

<img src="docs/static/8wave.svg" alt="Logo" width="130"/>

</div>

## Introduction
Factotum is a Laravel-based open source CMS and application framework. It provides a modular architecture, a user-friendly interface, and a set of tools to build and manage web applications efficiently.
<br>

This repository contains the base module of Factotum, which includes essential features and functionalities required for building applications, as authentication, roles and permissions, media handling and more.

## Requirements and Dependencies
- Laravel 12+
- PHP 8.4+


## Install
### Laravel Setup

1. Install a fresh Laravel application and **configure your .env file** with the database keys.
```bash
# composer
composer create-project laravel/laravel example-app
```

2. Require Factotum Base Module as a composer dependency and publish the configuration file. <br>The configuration file is used to seed the initial data, feel free to change its values

```bash
# composer
composer require wave8/factotum-base

# config
php artisan vendor:publish --tag=factotum-base-config
```
3. On the default User laravel model, you need to extend the `\Wave8\Factotum\Base\Models\User` model. 
```diff
app/Models/User.php

-class User extends Authenticatable
+class User extends \Wave8\Factotum\Base\Models\User
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
```
4. Install the Factotum Base Module. This procedure will run the migrations, seed the initial data and publish the assets.
```bash
# php
php artisan factotum-base:install
```

## Please note
Some vendor config files, are published in an updated version during the installation, i.e.
- `spatie/laravel-query-builder`

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Feel free to open issues and submit pull requests.

## Security

If you discover any security related issues, please feel free to open issues and submit pull requests.

## Credits

- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
