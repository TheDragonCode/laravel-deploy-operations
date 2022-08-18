<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

class Install extends Processor
{
    public function handle()
    {
        $this->repository->createRepository();

        $this->notification()->info('Actions table created successfully.');
    }
}
