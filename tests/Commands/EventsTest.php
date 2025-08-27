<?php

declare(strict_types=1);

namespace Tests\Commands;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Events\DeployOperationEnded;
use DragonCode\LaravelDeployOperations\Events\DeployOperationFailed;
use DragonCode\LaravelDeployOperations\Events\DeployOperationStarted;
use Exception;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Throwable;

class EventsTest extends TestCase
{
    public function testSuccess(): void
    {
        $this->copyFiles();

        Event::fake();

        $this->artisan(Names::Operations)->assertExitCode(0);

        Event::assertDispatchedTimes(DeployOperationStarted::class, 1);
        Event::assertDispatchedTimes(DeployOperationEnded::class, 1);

        Event::assertNotDispatched(DeployOperationFailed::class);
    }

    public function testFailed(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Custom exception');

        $this->copyFailedMethod();

        Event::fake();

        try {
            $this->artisan(Names::Operations)->run();
        } catch (Throwable $e) {
            Event::assertDispatchedTimes(DeployOperationStarted::class, 1);
            Event::assertDispatchedTimes(DeployOperationFailed::class, 1);

            Event::assertNotDispatched(DeployOperationEnded::class);

            throw $e;
        }
    }
}
