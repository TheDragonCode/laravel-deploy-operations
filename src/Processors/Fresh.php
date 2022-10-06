<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;

class Fresh extends Processor
{
    public function handle(): void
    {
        $this->drop();
        $this->create();
        $this->migrate();
    }

    protected function drop(): void
    {
        if ($this->repository->repositoryExists()) {
            $this->notification->task('Dropping all actions', fn () => $this->repository->deleteRepository());
        }
    }

    protected function create(): void
    {
        $this->runCommand(Names::INSTALL, [
            '--' . Options::CONNECTION => $this->options->connection,
            '--' . Options::FORCE      => $this->options->force,
        ]);
    }

    protected function migrate(): void
    {
        $this->runCommand(Names::MIGRATE, [
            '--' . Options::CONNECTION => $this->options->connection,
        ]);
    }
}
