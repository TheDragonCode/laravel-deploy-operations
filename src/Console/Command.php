<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Concerns\ConfirmableTrait;
use DragonCode\LaravelActions\Concerns\Optionable;
use DragonCode\LaravelActions\Processors\Processor;
use DragonCode\LaravelActions\Values\Options as OptionsDto;
use Illuminate\Console\Command as BaseCommand;
use Illuminate\Container\Container;

abstract class Command extends BaseCommand
{
    use ConfirmableTrait;
    use Optionable;

    protected Processor|string $processor;

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

    protected function getOptionsDto(): OptionsDto
    {
        return OptionsDto::fromArray(array_merge($this->options(), $this->arguments()));
    }
}
