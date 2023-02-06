# Installation

To get the latest version of `Laravel Actions`, simply require the project using [Composer](https://getcomposer.org):

```bash
composer require dragon-code/laravel-actions
```

Or manually update `require` block of `composer.json` and run `composer update` console command.

```json
{
    "require": {
        "dragon-code/laravel-actions": "^4.0"
    }
}
```

## Laravel Framework

Run the `php artisan vendor:publish --provider="DragonCode\LaravelActions\ServiceProvider"` console command for the config file publishing.

## Lumen Framework

This package is focused on Laravel development, but it can also be used in Lumen with some workarounds. Because Lumen works a little different, as it is like a barebone version of
Laravel and the main configuration parameters are instead located in `bootstrap/app.php`, some alterations must be made.

You can install `Laravel Actions` in `app/Providers/AppServiceProvider.php`, and uncommenting this line that registers the App Service Providers so it can properly load.

```php
// $app->register(App\Providers\AppServiceProvider::class);
```

If you are not using that line, that is usually handy to manage gracefully multiple Lumen installations, you will have to add this line of code under
the `Register Service Providers` section of your `bootstrap/app.php`.

```php
$app->register(\DragonCode\LaravelActions\ServiceProvider::class);
```

Next, you can copy the config file:

```bash
cp vendor/dragon-code/laravel-actions/config/actions.php config/actions.php
```
