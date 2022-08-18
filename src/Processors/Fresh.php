<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;

class Fresh extends Processor
{
    public function handle()
    {
        $this->drop();
        $this->migrate();
    }

    protected function drop(): void
    {
        $this->repository->deleteRepository();

        $this->notification()->info('Action table deleted successfully.');
    }

    protected function migrate(): void
    {
        $this->artisan(Names::MIGRATE, $this->getMigrateParams());

        $this->notification()->info('Migration table created successfully.');
    }

    protected function getMigrateParams(): array
    {
        return array_filter([
            '--' . Options::DATABASE => $this->options->database,
            '--' . Options::FORCE    => true,
        ]);
    }
}
