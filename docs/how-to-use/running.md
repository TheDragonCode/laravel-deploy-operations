# Running Actions

To run all of your outstanding actions, execute the `actions` artisan command:

```bash
php artisan actions
```

Action call order is checked by filename without path:

```bash
2022_10_14_000001_test1      # 1
2022_10_14_000004_test4      # 4
bar/2022_10_14_000003_test3  # 3
foo/2022_10_14_000002_test2  # 2
```

```bash
2022_10_14_000001_test1      # 1
foo/2022_10_14_000002_test2  # 2
bar/2022_10_14_000003_test3  # 3
2022_10_14_000004_test4      # 4
```

## Isolating Action Execution

If you are deploying your application across multiple servers and running actions as part of your deployment process, you likely do not want two servers attempting to run
the database at the same time. To avoid this, you may use the `isolated` option when invoking the `actions` command.

When the `isolated` option is provided, Laravel will acquire an atomic lock using your application's cache driver before attempting to run your actions. All other attempts to
run the `actions` command while that lock is held will not execute; however, the command will still exit with a successful exit status code:

```bash
php artisan actions --isolated
```

## Split Launch Option

Sometimes it becomes necessary to launch actions separately, for example, to notify about the successful deployment of a project.

There is a `before` option for this when calling actions:

```bash
php artisan actions --before
```

When calling the `actions` command with the `before` parameter, the script will execute only those actions within which the value of the `before` parameter is `true`.

For backwards compatibility, the `before` parameter is set to `true` by default, but actions will only be executed if the option is explicitly passed.

```php
use DragonCode\LaravelActions\Action;

return new class extends Action
{
    protected bool $before = false;

    public function __invoke(): void
    {
        // some code
    }
};
```

For example, you need to call actions when deploying an application. Some actions should be run after the actions are deployed, and others after the application is fully
launched.

To run, you need to pass the `before` parameter. For example, when using [`deployer`](https://github.com/deployphp/deployer) it would look like this:

```php
task('deploy', [
    // ...
    'artisan:migrate',
    'artisan:actions --before', // here
    'deploy:publish',
    'php-fpm:reload',
    'artisan:queue:restart',
    'artisan:actions', // here
]);
```

Thus, when `actions` is called, all actions whose `before` parameter is `true` will be executed, and after that, the remaining tasks will be executed.

> Note:
> If you call the `actions` command without the `before` parameter,
> then all tasks will be executed regardless of the value of the `$before`
> attribute inside the action class.

## Forcing Actions To Run In Production

> Some commands cannot be executed in production without confirmation.
> These include all commands except `actions:status` and `actions`.

Some action operations are destructive, which means they may cause you to lose data. In order to protect you from running these commands against your production database,
you will be prompted for confirmation before the commands are executed. To force the commands to run without a prompt, use the `--force` flag:

```bash
php artisan actions:install --force
```

## Execution Every Time

In some cases, you need to call the code every time you deploy the application. For example, to call reindexing.

To do this, override the `$once` variable in the action file:

```php
use DragonCode\LaravelActions\Action;

return new class extends Action
{
    protected bool $once = false;

    public function __invoke(): void
    {
        // some code
    }
};
```

If the value is `$once = false`, the `up` method will be called every time the `actions` command called.

In this case, information about it will not be written to the `actions` table and, therefore, the `down` method will not be called when the rollback command is called.

> Note
>
> When using the `before` parameter to run command, it is recommended to override the value of the `$before` attribute to `false`, otherwise this action will be executed twice.

## Execution In A Specific Environment

In some cases, it becomes necessary to execute an action in a specific environment. For example `production`.

For this you can use the `$environment` parameter:

```php
use DragonCode\LaravelActions\Action;

return new class extends Action
{
    protected string|array|null $environment = 'production';

    public function __invoke(): void
    {
        // some code
    }
};
```

You can also specify multiple environment names:

```php
use DragonCode\LaravelActions\Action;

return new class extends Action
{
    protected string|array|null $environment = ['testing', 'staging'];

    public function __invoke(): void
    {
        // some code
    }
};
```

By default, the action will run in all environments. The same will happen if you specify `null` or `[]` as the value.

## Execution Excluding Certain Environments

In some cases, it becomes necessary to execute an action excluding certain environments. For example `production`.

For this you can use the `$except_environment` parameter:

```php
use DragonCode\LaravelActions\Action;

return new class extends Action
{
    protected string|array|null $exceptEnvironment = 'production';

    public function __invoke(): void
    {
        // some code
    }
};
```

You can also specify multiple environment names:

```php
use DragonCode\LaravelActions\Action;

return new class extends Action
{
    protected string|array|null $exceptEnvironment = ['testing', 'staging'];

    public function __invoke(): void
    {
        // some code
    }
};
```

By default, no actions will be excluded. The same happens if you specify `null` or `[]` value.

## Database Transactions

In some cases, it becomes necessary to undo previously performed actions in the database. For example, when code execution throws an error. To do this, the code must be wrapped in
a transaction.

By setting the `$transactions = true` parameter, you will ensure that your code is wrapped in a transaction without having to manually call the `DB::transaction()` method. This
will reduce the time it takes to create the action.

```php
use DragonCode\LaravelActions\Action;

return new class extends Action
{
    protected bool $transactions = true;

    protected int $transactionAttempts = 3;

    public function __invoke(): void
    {
        // some code
    }
};
```

## Asynchronous Call

In some cases, it becomes necessary to execute actions in an asynchronous manner without delaying the deployment process.

To do this, you need to override the `$async` property in the action class:

```php
use DragonCode\LaravelActions\Action;

return new class extends Action
{
    protected bool $async = true;

    public function __invoke(): void
    {
        // some code
    }
};
```

In this case, the action file that defines this parameter will run asynchronously using the `DragonCode\LaravelActions\Jobs\ActionJob` class.

The name of the connection and queue can be changed through the [settings](https://github.com/TheDragonCode/laravel-actions/tree/main/config).

::: Info
We remind you that in this case the [queuing system](https://laravel.com/docs/queues) must work in your application.

Using Laravel version 8.37 and above, checking for the [uniqueness](https://laravel.com/docs/10.x/queues#unique-jobs) of the execution is supported.
:::
