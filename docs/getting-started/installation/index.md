# Installation

To get the latest version of `Deploy Actions for Laravel`, simply require the project using [Composer](https://getcomposer.org):

```bash
composer require dragon-code/laravel-actions
```

Or manually update `require` block of `composer.json` and run `composer update` console command.

```json
{
    "require": {
        "dragon-code/laravel-actions": "^5.0"
    }
}
```

If necessary, you can publish the configuration file by calling the console command:

```bash
php artisan vendor:publish --provider="DragonCode\LaravelActions\ServiceProvider"
```
