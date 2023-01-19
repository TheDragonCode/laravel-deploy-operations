<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Concerns\ConfirmableTrait;
use DragonCode\LaravelActions\Concerns\Isolatable;
use DragonCode\LaravelActions\Concerns\Optionable;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Processor;
use DragonCode\LaravelActions\Values\Options as OptionsDto;
use Illuminate\Console\Command as BaseCommand;
use Illuminate\Container\Container;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends BaseCommand
{
    use ConfirmableTrait;
    use Isolatable;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->option(Options::ISOLATED) !== false && ! $this->isolationCreate()) {
            $this->comment(sprintf('The [%s] command is already running.', $this->getName()));

            return $this->isolatedStatusCode();
        }

        try {
            return parent::execute($input, $output);
        }
        finally {
            if ($this->option(Options::ISOLATED) !== false) {
                $this->isolationForget();
            }
        }
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
        return OptionsDto::fromArray(array_merge($this->options(), $this->arguments()))->resolvePath();
    }
}
