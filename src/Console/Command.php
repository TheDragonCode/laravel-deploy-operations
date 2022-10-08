<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Concerns\ConfirmableTrait;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Processor;
use DragonCode\LaravelActions\Values\Options as OptionsDto;
use DragonCode\Support\Facades\Helpers\Arr;
use Illuminate\Console\Command as BaseCommand;
use Illuminate\Container\Container;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class Command extends BaseCommand
{
    use ConfirmableTrait;

    protected Processor|string $processor;

    protected array $arguments = [];

    protected array $options = [
        Options::CONNECTION,
        Options::FORCE,
    ];

    public function handle(): int
    {
        if ($this->allowToProceed()) {
            $this->resolveProcessor()->handle();
            $this->forgetProcessor();

            return self::SUCCESS;
        }

        return self::FAILURE;
    }

    protected function resolveProcessor(): Processor
    {
        return $this->container()->make($this->processor, [
            'options' => $this->getOptionsDto(),
            'input'   => $this->input,
            'output'  => $this->output,
        ]);
    }

    protected function forgetProcessor(): void
    {
        $this->container()->forgetInstance($this->processor);
    }

    protected function container(): Container
    {
        return Container::getInstance();
    }

    protected function configure(): void
    {
        foreach ($this->getFilteredArguments() as $argument) {
            $this->addArgument(...$argument);
        }
    }

    protected function getOptionsDto(): OptionsDto
    {
        return OptionsDto::fromArray(array_merge($this->options(), $this->arguments()));
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
            [Options::PATH, null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'The path(s) to the actions files to be executed'],
            [Options::REALPATH, null, InputOption::VALUE_NONE, 'Indicate any provided action file paths are pre-resolved absolute paths'],
            [Options::STEP, null, InputOption::VALUE_OPTIONAL, 'Force the actions to be run so they can be rolled back individually'],
        ];
    }

    protected function getFilteredArguments(): array
    {
        return Arr::of($this->availableArguments())
            ->filter(fn (array $argument) => in_array($argument[0], $this->arguments))
            ->toArray();
    }

    protected function availableArguments(): array
    {
        return [
            [Options::NAME, InputArgument::OPTIONAL, 'The name of the action'],
        ];
    }
}
