<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Processor;
use DragonCode\Support\Facades\Helpers\Arr;
use Illuminate\Console\Command as BaseCommand;
use Illuminate\Container\Container;
use Symfony\Component\Console\Input\InputOption;

abstract class Command extends BaseCommand
{
    protected Processor|string $processor;

    protected array $options = [
        Options::CONNECTION,
        Options::FORCE,
    ];

    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        $this->resolveProcessor()->handle();

        return 0;
    }

    protected function resolveProcessor(): Processor
    {
        return Container::getInstance()->make($this->processor, [
            'options' => $this->options(),
            'input'   => $this->input,
            'output'  => $this->output,
        ]);
    }

    protected function confirmToProceed(): bool
    {
        if ($this->optionForce()) {
            return true;
        }

        $this->warn('Application in production');

        if ($this->confirm('Do you really wish to run this command?')) {
            return true;
        }

        $this->warn('Command canceled');

        return false;
    }

    protected function getOptions(): array
    {
        return Arr::of($this->availableOptions())
            ->filter(fn (array $option) => in_array($option[0], $this->options))
            ->toArray();
    }

    protected function availableOptions(): array
    {
        return [
            [Options::BEFORE, null, InputOption::VALUE_NONE, 'Run actions marked as before'],
            [Options::CONNECTION, null, InputOption::VALUE_OPTIONAL, 'The database connection to use'],
            [Options::FORCE, null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],
            [Options::NAME, null, InputOption::VALUE_OPTIONAL, 'The name of the action'],
            [Options::PATH, '*', InputOption::VALUE_IS_ARRAY, 'The path(s) to the migrations files to be executed'],
            [Options::REALPATH, null, InputOption::VALUE_NONE, 'Indicate any provided migration file paths are pre-resolved absolute paths'],
            [Options::STEP, null, InputOption::VALUE_OPTIONAL, 'Force the actions to be run so they can be rolled back individually'],
        ];
    }
}
