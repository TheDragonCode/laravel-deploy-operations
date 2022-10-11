# Creating Actions

To create an action use the `make:migration:action` artisan command:

```bash
php artisan make:migration:action some_name
```

The new action's file will be placed in your `actions` directory in the base path of your app.

Each action file name contains a timestamp, which allows Laravel to determine the order of the actions.


## Automatically Generate A File Name

If you do not specify the "name" attribute, then the file name will be generated automatically according to the rule:

> git branch name ?: 'auto'

```bash
php artisan make:migration:action

### When the git repository is found (`base_path('.git')` directory is exists).
### For example, HEAD branch name is 'qwerty'.
# 2022_10_11_225116_qwerty.php
# 2022_10_11_225118_qwerty.php
# 2022_10_11_225227_qwerty.php

### When the git repository is not found (`base_path('.git')` directory doesn't exists).
### For example, HEAD branch name is 'qwerty'.
# 2022_10_11_225116_auto.php
# 2022_10_11_225118_auto.php
# 2022_10_11_225227_auto.php
```

## Nested Files

Since version `3.0` you can use nested paths to create actions:

```bash
php artisan make:migration:action Foo/Bar/QweRty
php artisan make:migration:action Foo/Bar/QweRty.php

php artisan make:migration:action Foo\Bar\QweRty
php artisan make:migration:action Foo\Bar\QweRty.php

php artisan make:migration:action foo\bar\QweRty
php artisan make:migration:action foo\bar\QweRty.php
```

All of these commands will create a file called `actions/foo/bar/Y_m_d_His_qwe_rty.php`.

For example:

```bash
php artisan make:migration:action foo\bar\QweRty
# actions/foo/bar/2022_10_11_225734_qwe_rty.php

php artisan make:migration:action foo\bar\QweRty.php
# actions/foo/bar/2022_10_11_225734_qwe_rty.php

php artisan make:migration:action foo/bar/QweRty
# actions/foo/bar/2022_10_11_225734_qwe_rty.php

php artisan make:migration:action foo/bar/QweRty.php
# actions/foo/bar/2022_10_11_225734_qwe_rty.php
```

## Invokable Method

By default, the new action class will contain the `__invoke` method, but you can easily replace it with public `up` name.

```php
use DragonCode\LaravelActions\Action;

return new class () extends Action
{
    public function __invoke(): void
    {
        // some code
    }
};
```

> Note that the `__invoke` method has been added as a single call.
> This means that when the action is running, it will be called, but not when it is rolled back.
>
> You should also pay attention to the fact that if there is an `__invoke` method in the class, the `down` method will not be called.

```php
use DragonCode\LaravelActions\Action;

return new class () extends Action
{
    public function __invoke(): void {} // called when `php artisan migrate:actions` running

    public function down(): void {} // doesn't call when `php artisan migrate:rollback` running
                                    // and any other commands to revert the action.  
};
```

## Dependency Injection

You can also use the dependency injection with `__invoke`, `up` and `down` methods:

```php
use DragonCode\LaravelActions\Action;
use Tests\Concerns\Some;

return new class () extends Action
{
    public function __invoke(Some $some): void
    {
        $value = $some->get('qwerty');
    }
};
```

```php
use DragonCode\LaravelActions\Action;
use Tests\Concerns\Some;

return new class () extends Action
{
    public function up(Some $some): void
    {
        $value = $some->get('qwerty');
    }

    public function down(Some $some): void
    {
        $value = $some->get('qwerty');
    }
};
```
