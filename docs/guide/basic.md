# Basic Usage

Create your first operation using `make:operation` command and define the actions it should perform.

```bash
php artisan make:operation
```

```php
use DragonCode\LaravelDeployOperations\Operation;

return new class extends Operation {
    public function __invoke(): void
    {
        // any actions
    }
};
```

Next, To run operations, execute the `operations` artisan command:

```bash
php artisan operations
```

The order in which operations are called is checked by file name in alphabetical order,
without taking into account directory names:

```bash
# actual file names
2022_10_14_000001_test1      # 1
2022_10_14_000004_test4      # 4
bar/2022_10_14_000003_test3  # 3
foo/2022_10_14_000002_test2  # 2
```

```bash
# order of running operations at startup
2022_10_14_000001_test1      # 1
foo/2022_10_14_000002_test2  # 2
bar/2022_10_14_000003_test3  # 3
2022_10_14_000004_test4      # 4
```

In addition to other options described in the "Guide" section, you can divide the execution of operations into
"before" and "after" certain actions.
For example, before and after restarting the queues:

```bash
php artisan operations --before
php artisn queue:restart
php artisan operations
```
