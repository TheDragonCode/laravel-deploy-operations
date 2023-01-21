<?php

declare(strict_types=1);

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Events\ActionEnded;
use DragonCode\LaravelActions\Events\ActionFailed;
use DragonCode\LaravelActions\Events\ActionStarted;
use Exception;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Throwable;

class EventsTest extends TestCase
{
    public function testSuccess()
    {
        $this->copyFiles();

        Event::fake();

        $this->artisan(Names::ACTIONS)->assertSuccessful();

        Event::assertDispatchedTimes(ActionStarted::class, 1);
        Event::assertDispatchedTimes(ActionEnded::class, 1);

        Event::assertNotDispatched(ActionFailed::class);
    }

    public function testFailed()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Custom exception');

        $this->copyFailedMethod();

        Event::fake();

        try {
            $this->artisan(Names::ACTIONS)->run();
        }
        catch (Throwable $e) {
            Event::assertDispatchedTimes(ActionStarted::class, 1);
            Event::assertDispatchedTimes(ActionFailed::class, 1);

            Event::assertNotDispatched(ActionEnded::class);

            throw $e;
        }
    }
}
