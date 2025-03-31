<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Processors;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Constants\Options;

class FreshProcessor extends Processor
{
    public function handle(): void
    {
        $this->drop();
        $this->operations();
    }

    protected function drop(): void
    {
        if ($this->repository->repositoryExists()) {
            $this->notification->task('Dropping all operations', fn () => $this->repository->deleteRepository());
        }
    }

    protected function operations(): void
    {
        $this->runCommand(Names::Operations, [
            '--' . Options::Connection => $this->options->connection,
            '--' . Options::Path       => $this->options->path,
            '--' . Options::Realpath   => true,
        ]);
    }
}
