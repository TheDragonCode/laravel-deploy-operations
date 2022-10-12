# Upgrade Guide

## High Impact Changes

- Replacing named classes with anonymous ones
- Change the location of the configuration file
- Changing the namespace of the parent class
- Changing variable names from `snake_case` to `camelCase`
- Added recursive search for actions in a folder
- PHP 7.3 and 7.4 was dropped
- Laravel 6.0 was dropped
- Dragon Code: Contracts (`dragon-code/contracts`) was dropped

## Medium Impact Changes

- Changing the name of an action column in the database
- Action storage directory changed

## Upgrading To 3.x from 2.x

### Updating Dependencies

#### PHP 8.0.2 Required

Laravel Actions now requires PHP 8.0.2 or greater.

#### Composer Dependencies

You should update the following dependency in your application's `composer.json` file:

- `dragon-code/laravel-migration-actions` to `^3.0`

### Call Upgrade Command

For your convenience, we have created an upgrade console command:

```bash
php artisan migrate:actions:upgrade
```

It will do the following:

- Change the namespace of the abstract class
- Add a strict type declaration
- Replace the `up` method with `__invoke` if the class does not have a `down` method
- Replace named classes with anonymous ones
- Create a configuration file according to the data saved in your project

> Note
> If you used inheritance of actions from other actions, then you will need to process these files manually.

### Configuration

Publish the config file and migrate the settings from the `config/database.php` file.

```bash
php artisan vendor:publish --provider="DragonCode\LaravelActions\ServiceProvider"
```

### Actions Location

Move the action files to the `actions` folder in the project root, or update the `actions.path` option in the configuration file.


### Parent Namespace

Replace `DragonCode\LaravelActions\Support\Actionable` with `DragonCode\LaravelActions\Action`.

### Anonymous Classes

Replace named calls to your application's classes with anonymous ones.

For example:

```php
// before
use DragonCode\LaravelActions\Support\Actionable;

class Some extends Actionable {}

// after
use DragonCode\LaravelActions\Action;

return new class () extends Action {};
```

### Invokable Method

If your class does not contain a `down` method, then you can replace the `up` method with `__invoke`.

### Changed Migration Repository

Just call the `php artisan migrate` command to make changes to the action repository table. 
