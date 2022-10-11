# Events

You can also handle events when executing actions:

```
DragonCode\LaravelActions\Events\ActionStarted
DragonCode\LaravelActions\Events\ActionEnded
DragonCode\LaravelActions\Events\ActionFailed
DragonCode\LaravelActions\Events\NoPendingActions
```

If there are no action files to execute, the `NoPendingActions` event will be sent.

In other cases, the `ActionStarted` event will be sent before processing starts, and the `ActionEnded` event will be sent after processing.

For example:

```php
namespace App\Providers;

use App\Listeners\SomeActionsListener;
use DragonCode\LaravelActions\Events\ActionEnded;
use DragonCode\LaravelActions\Events\ActionStarted;
use DragonCode\LaravelActions\Events\NoPendingActions;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ActionStarted::class    => [SomeActionsListener::class],
        ActionEnded::class      => [SomeActionsListener::class],
        ActionFailed::class     => [SomeActionsListener::class],
        NoPendingActions::class => [SomeActionsListener::class],
    ];
}
```

```php
namespace App\Listeners;

use DragonCode\LaravelActions\Events\BaseEvent;

class SomeActionsListener
{
    public function handle(BaseEvent $event): void
    {
        $method   = $event->method; // `up` or `down` string value
        $isBefore = $event->before; // boolean
    }
}
```
