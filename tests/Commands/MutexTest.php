<?php

declare(strict_types=1);

namespace Tests\Commands;

use DragonCode\LaravelDeployOperations\Console\Command;
use DragonCode\LaravelDeployOperations\Constants\Options;
use DragonCode\LaravelDeployOperations\Services\MutexService;
use Illuminate\Container\Container;
use Mockery as m;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Tests\TestCase;

class MutexTest extends TestCase
{
    protected Command $command;

    protected MutexService $mutex;

    protected function setUp(): void
    {
        $this->command = new class extends Command {
            public int $ran = 0;

            public function handle(): int
            {
                ++$this->ran;

                return self::SUCCESS;
            }
        };

        $this->mutex = m::mock(MutexService::class);

        $container = Container::getInstance();
        $container->instance(MutexService::class, $this->mutex);
        $this->command->setLaravel($container);
    }

    public function testCanRunIsolatedCommandIfNotBlocked(): void
    {
        $this->mutex->shouldReceive('create')
            ->andReturn(true)
            ->once();

        $this->mutex->shouldReceive('forget')
            ->once();

        $this->runCommand();

        $this->assertSame(1, $this->command->ran);
    }

    public function testCannotRunIsolatedCommandIfBlocked(): void
    {
        $this->mutex->shouldReceive('create')
            ->andReturn(false)
            ->once();

        $this->mutex->shouldReceive('forget')
            ->never();

        $this->runCommand();

        $this->assertSame(0, $this->command->ran);
    }

    public function testCanRunCommandAgainAfterOtherCommandFinished(): void
    {
        $this->mutex->shouldReceive('create')
            ->andReturn(true)
            ->twice();

        $this->mutex->shouldReceive('forget')
            ->twice();

        $this->runCommand();
        $this->runCommand();

        $this->assertEquals(2, $this->command->ran);
    }

    public function testCanRunCommandAgainNonAutomated(): void
    {
        $this->mutex->shouldNotHaveBeenCalled();

        $this->runCommand(false);

        $this->assertEquals(1, $this->command->ran);
    }

    protected function runCommand($withIsolated = true)
    {
        $input  = new ArrayInput(['--' . Options::Isolated => $withIsolated]);
        $output = new NullOutput();

        $this->command->run($input, $output);
    }
}
