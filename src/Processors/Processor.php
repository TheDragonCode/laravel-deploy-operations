<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use Closure;
use DragonCode\LaravelActions\Concerns\Artisan;
use DragonCode\LaravelActions\Helpers\Config;
use DragonCode\LaravelActions\Helpers\Git;
use DragonCode\LaravelActions\Notifications\Notification;
use DragonCode\LaravelActions\Repositories\ActionRepository;
use DragonCode\LaravelActions\Services\Migrator;
use DragonCode\LaravelActions\Values\Options;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Filesystem\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Processor
{
    use Artisan;

    abstract public function handle(): void;

    public function __construct(
        protected Options          $options,
        protected InputInterface   $input,
        protected OutputInterface  $output,
        protected Config           $config,
        protected ActionRepository $repository,
        protected Git              $git,
        protected File             $file,
        protected Migrator         $migrator,
        protected Notification     $notification
    ) {
    }

    protected function getFiles(Closure $filter): array
    {
        $files = $this->file->names($this->config->path(), $filter, true);

        return Arr::sort($files);
    }

    protected function runCommand(string $command, array $options = []): void
    {
        $this->artisan($command, array_filter($options));
    }
}
