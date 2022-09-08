<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\Concerns\Composer;
use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Helpers\Str;

class Make extends Processor
{
    use Composer;

    public function handle()
    {
        $this->writeMigration(
            Str::snake($this->options->name)
        );

        $this->composer()->dumpAutoloads();
    }

    protected function writeMigration(string $name)
    {
        File::copy(__DIR__ . '/../../resources/stubs/action.stub', base_path('actions/' . $name));

        $this->notification()->info("Created Action: $name");
    }
}
