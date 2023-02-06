# Execution Status

You can also override the `success` and `failed` methods, which are called on success or failure processing.

## If Success

```php
use DragonCode\LaravelActions\Action;
use Illuminate\Support\Facade\Log;

return new class () extends Action
{
    public function up(): void
    {
       //
    }

    public function down(): void
    {
       //
    }

    public function success(): void
    {
       Log::info('success');
    }

    public function failed(): void
    {
       Log::info('failed');
    }
};
```

Call the `php artisan actions` command.

The log file will contain two `success` entries.

## If Failed

```php
use DragonCode\LaravelActions\Action;
use Exeption;
use Illuminate\Support\Facade\Log;

return new class extends Action
{
    public function up(): void
    {
       throw new Exeption();
    }

    public function down(): void
    {
       throw new Exeption();
    }

    public function success(): void
    {
       Log::info('success');
    }

    public function failed(): void
    {
       Log::info('failed');
    }
};
```

Call the `php artisan actions` command.

The log file will contain two `failed` entries.
