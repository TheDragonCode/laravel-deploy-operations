<?php

declare(strict_types=1);

namespace Tests\Commands;

use DragonCode\LaravelActions\Console\Command;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Services\Mutex;
use Illuminate\Container\Container;
use Mockery as m;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Tests\TestCase;

class MutexTest extends TestCase
{
    protected Command $command;

    protected Mutex $mutex;

    public function testCanRunIsolatedCommandIfNotBlocked()
    {
        $this->mutex->shouldReceive('create')
            ->andReturn(true)
            ->once();

        $this->mutex->shouldReceive('forget')
            ->never()
            ->once();

        $this->runCommand();

        $this->assertSame(1, $this->command->ran);
    }

    public function testCannotRunIsolatedCommandIfBlocked()
    {
        $this->mutex->shouldReceive('create')
            ->andReturn(false)
            ->once();

        $this->mutex->shouldReceive('forget')
            ->never()
            ->once();

        $this->runCommand();

        $this->assertSame(0, $this->command->ran);
    }

    public function testCanRunCommandAgainAfterOtherCommandFinished()
    {
        $this->mutex->shouldReceive('create')
            ->andReturn(true)
            ->twice();

        $this->mutex->shouldReceive('forget')
            ->never()
            ->twice();

        $this->runCommand();
        $this->runCommand();

        $this->assertEquals(2, $this->command->ran);
    }

    public function testCanRunCommandAgainNonAutomated()
    {
        $this->mutex->shouldNotHaveBeenCalled();

        $this->runCommand(false);

        $this->assertEquals(1, $this->command->ran);
    }

    protected function setUp(): void
    {
        $this->command = new class extends Command
        {
            public int $ran = 0;

            public function handle(): int
            {
                $this->ran++;

                return self::SUCCESS;
            }
        };

        $this->mutex = m::mock(Mutex::class);

        $container = Container::getInstance();
        $container->instance(Mutex::class, $this->mutex);
        $this->command->setLaravel($container);
    }

    protected function runCommand($withIsolated = true)
    {
        $input  = new ArrayInput(['--' . Options::ISOLATED => $withIsolated]);
        $output = new NullOutput;

        $this->command->run($input, $output);
    }
}
