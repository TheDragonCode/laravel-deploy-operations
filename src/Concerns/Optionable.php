<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelActions\Constants\Options;
use DragonCode\Support\Facades\Helpers\Arr;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/** @mixin \DragonCode\LaravelActions\Console\Command */
trait Optionable
{
    protected array $arguments = [];

    protected array $options = [
        Options::CONNECTION,
        Options::FORCE,
        Options::SILENT,
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
            [Options::BEFORE, null, InputOption::VALUE_NONE, 'Run actions marked as before'],
            [Options::CONNECTION, null, InputOption::VALUE_OPTIONAL, 'The database connection to use'],
            [Options::FORCE, null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],
            [Options::PATH, null, InputOption::VALUE_OPTIONAL, 'The path to the actions files to be executed'],
            [Options::REALPATH, null, InputOption::VALUE_NONE, 'Indicate any provided action file paths are pre-resolved absolute path'],
            [Options::STEP, null, InputOption::VALUE_OPTIONAL, 'Force the actions to be run so they can be rolled back individually'],
            [Options::SILENT, null, InputOption::VALUE_NONE, 'Turns off the output of informational messages'],
        ];
    }

    protected function availableArguments(): array
    {
        return [
            [Options::NAME, InputArgument::OPTIONAL, 'The name of the action'],
        ];
    }
}
