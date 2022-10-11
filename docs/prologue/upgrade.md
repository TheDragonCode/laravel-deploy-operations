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
