<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Concerns\Notifications;
use DragonCode\LaravelActions\Concerns\Optionable;
use DragonCode\LaravelActions\Concerns\Path;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Processor;
use Illuminate\Console\Command as BaseCommand;
use Illuminate\Container\Container;
use Symfony\Component\Console\Input\InputOption;

abstract class Command extends BaseCommand
{
    use Optionable;
    use Notifications;
    use Path;

    protected Processor|string $processor;

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
            'options' => $this->optionDto(),
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
        return [
            [Options::FORCE, null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],

            [Options::DATABASE, null, InputOption::VALUE_OPTIONAL, 'The database connection to use'],
        ];
    }
}
