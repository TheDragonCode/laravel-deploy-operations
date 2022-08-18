<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use Illuminate\Support\Facades\Schema;

class Fresh extends Processor
{
    public function handle()
    {
        $this->drop();
        $this->migrate();
    }

    protected function drop(): void
    {
        Schema::dropIfExists($this->getTableName());
    }

    protected function migrate(): void
    {
        $this->artisan(Names::MIGRATE, $this->getMigrateParams());
    }

    protected function getMigrateParams(): array
    {
        return array_filter([
            '--' . Options::DATABASE => $this->options->database,
            '--' . Options::FORCE    => true,
        ]);
    }
}
