<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Concerns;

use DragonCode\LaravelDeployOperations\Constants\Options;
use DragonCode\Support\Facades\Helpers\Arr;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function in_array;

/** @mixin \DragonCode\LaravelDeployOperations\Console\Command */
trait HasOptionable
{
    protected array $arguments = [];

    protected array $options = [
        Options::Connection,
        Options::Force,
        Options::Mute,
        Options::Isolated,
    ];

    protected function configure(): void
    {
        $this->specifyParameters();
    }

    protected function getOptions(): array
    {
        return Arr::of($this->availableOptions())
            ->filter(fn (array $option) => in_array($option[0], $this->options))
            ->toArray();
    }

    protected function getArguments(): array
    {
        return Arr::of($this->availableArguments())
            ->filter(fn (array $argument) => in_array($argument[0], $this->arguments))
            ->toArray();
    }

    protected function availableOptions(): array
    {
        return [
            [
                Options::Before,
                null,
                InputOption::VALUE_NONE,
                'Run operations marked as before',
            ],
            [
                Options::Connection,
                null,
                InputOption::VALUE_OPTIONAL,
                'The database connection to use',
            ],
            [
                Options::Force,
                null,
                InputOption::VALUE_NONE,
                'Force the operation to run when in production',
            ],
            [
                Options::Path,
                null,
                InputOption::VALUE_OPTIONAL,
                'The path to the operations files to be executed',
            ],
            [
                Options::Realpath,
                null,
                InputOption::VALUE_NONE,
                'Indicate any provided operation file paths are pre-resolved absolute path',
            ],
            [
                Options::Step,
                null,
                InputOption::VALUE_OPTIONAL,
                'Force the operations to be run so they can be rolled back individually',
            ],
            [
                Options::Mute,
                null,
                InputOption::VALUE_NONE,
                'Turns off the output of informational messages',
            ],
            [
                Options::Isolated,
                null,
                InputOption::VALUE_OPTIONAL,
                'Do not run the operations command if another instance of the operations command is already running',
                false,
            ],
            [
                Options::Sync,
                null,
                InputOption::VALUE_OPTIONAL,
                'Makes all operations run synchronously',
                false,
            ],
        ];
    }

    protected function availableArguments(): array
    {
        return [
            [Options::Name, InputArgument::OPTIONAL, 'The name of the operation'],
        ];
    }
}
