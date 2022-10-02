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
    }

    protected function drop(): void
    {
        if ($this->repository->repositoryExists()) {
            $this->repository->deleteRepository();

            $this->notification->info('Actions repository successfully deleted.');
        }
    }

    protected function create(): void
    {
        $this->runCommand(Names::INSTALL, [
            '--' . Options::CONNECTION => $this->options->connection,
            '--' . Options::FORCE      => $this->options->force,
        ]);
    }
}
