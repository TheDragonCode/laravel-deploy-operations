# 🚀 Laravel Deploy Operations

![the dragon code laravel deploy operations](https://preview.dragon-code.pro/the-dragon-code/deploy-operations.svg?brand=laravel&mode=dark)

[![Stable Version][badge_stable]][link_packagist]
[![Total Downloads][badge_downloads]][link_packagist]
[![Github Workflow Status][badge_build]][link_build]
[![License][badge_license]][link_license]

⚡ **Performing any actions during the deployment process**

Create specific classes for a one-time or more-time usage, that can be executed automatically after each deployment.
Perfect for seeding or updating some data instantly after some database changes, feature updates, or perform any
actions.

This package is for you if...

- you regularly need to update specific data after you deploy new code
- you often perform jobs after deployment
- you sometimes forget to execute that one specific job and stuff gets crazy
- your code gets cluttered with jobs that are not being used anymore
- your co-workers always need to be reminded to execute that one job after some database changes
- you often seed or process data in a migration file (which is a big no-no!)

## Installation

To get the latest version of **Deploy Operations**, simply require the project using [Composer](https://getcomposer.org):

```Bash
composer require dragon-code/laravel-deploy-operations
```

## Documentation

📚 [Check out the full documentation to learn everything that Laravel Deploy Operations has to offer.][link_website]

## Basic Usage

Create your first operation using `php artisan make:operation` console command and define the actions it should
perform.

```php
use App\Models\Article;
use DragonCode\LaravelDeployOperations\Operation;

return new class extends Operation {
    public function __invoke(): void
    {
        Article::query()
            ->lazyById(chunkSize: 100, column: 'id')
            ->each->update(['is_active' => true]);

        // and/or any actions...
    }
};
```

Next, you can run the console command to start operations:

```Bash
php artisan operations
```

## Downloads Stats

This project has gone the way of several names, and here are the number of downloads of each of them:

- ![](https://img.shields.io/packagist/dt/dragon-code/laravel-deploy-operations?style=flat-square&label=dragon-code%2Flaravel-deploy-operations)
- ![](https://img.shields.io/packagist/dt/dragon-code/laravel-actions?style=flat-square&label=dragon-code%2Flaravel-actions)
- ![](https://img.shields.io/packagist/dt/dragon-code/laravel-migration-actions?style=flat-square&label=dragon-code%2Flaravel-migration-actions)
- ![](https://img.shields.io/packagist/dt/andrey-helldar/laravel-actions?style=flat-square&label=andrey-helldar%2Flaravel-actions)

## License

This package is licensed under the [MIT License](LICENSE).


[badge_build]:          https://img.shields.io/github/actions/workflow/status/TheDragonCode/laravel-deploy-operations/tests.yml?style=flat-square

[badge_downloads]:      https://img.shields.io/packagist/dt/dragon-code/laravel-deploy-operations.svg?style=flat-square

[badge_license]:        https://img.shields.io/packagist/l/dragon-code/laravel-deploy-operations.svg?style=flat-square

[badge_stable]:         https://img.shields.io/github/v/release/TheDragonCode/laravel-deploy-operations?label=packagist&style=flat-square

[link_build]:           https://github.com/TheDragonCode/laravel-deploy-operations/actions

[link_license]:         LICENSE

[link_packagist]:       https://packagist.org/packages/dragon-code/laravel-deploy-operations

[link_website]:         https://deploy-operations.dragon-code.pro
