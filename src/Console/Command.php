<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Console;

use DragonCode\LaravelDeployOperations\Concerns\ConfirmableTrait;
use DragonCode\LaravelDeployOperations\Concerns\HasIsolatable;
use DragonCode\LaravelDeployOperations\Concerns\HasOptionable;
use DragonCode\LaravelDeployOperations\Data\OptionsData;
use DragonCode\LaravelDeployOperations\Processors\Processor;
use Illuminate\Console\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function app;
use function array_merge;
use function sprintf;

abstract class Command extends BaseCommand
{
    use ConfirmableTrait;
    use HasIsolatable;
    use HasOptionable;

    protected Processor|string $processor;

    public function handle(): int
    {
        if ($this->allowToProceed()) {
            $this->resolveProcessor()->handle();

            return self::SUCCESS;
        }

        return self::FAILURE;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->getIsolateOption() !== false && ! $this->isolationMutex()->create($this)) {
            $this->comment(sprintf('The [%s] command is already running.', $this->getName()));

            return $this->isolatedStatusCode();
        }

        try {
            return parent::execute($input, $output);
        } finally {
            if ($this->getIsolateOption() !== false) {
                $this->isolationMutex()->forget($this);
            }
        }
    }

    protected function resolveProcessor(): Processor
    {
        return app($this->processor, [
            'options' => $this->getOptionsData(),
            'input'   => $this->input,
            'output'  => $this->output,
        ]);
    }

    protected function getOptionsData(): OptionsData
    {
        return OptionsData::from(array_merge($this->options(), $this->arguments()));
    }
}
