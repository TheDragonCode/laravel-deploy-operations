# Artisan Command

Quite often, when working with actions, it becomes necessary to run one or another console command, and each time you have to write the following code:

```php
use DragonCode\LaravelActions\Action;

return new class () extends Action
{
    public function __invoke(): void
    {
        $this->artisan('some_command', [
            // parameters
        ]);
    }
};
```
