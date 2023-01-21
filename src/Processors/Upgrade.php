<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use Closure;
use DragonCode\LaravelActions\Concerns\Anonymous;
use DragonCode\LaravelActions\Constants\Names;
use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Helpers\Str;

class Upgrade extends Processor
{
    use Anonymous;

    public function handle(): void
    {
        $this->run();
    }

    protected function getFiles(string $path, ?Closure $filter = null): array
    {
        return $this->file->names($path, $filter, true);
    }

    protected function run(): void
    {
        $this->moveFiles();
    }

    protected function moveFiles(): void
    {
        foreach ($this->getProjectFiles() as $filename) {
            $this->notification->task($filename, fn () => $this->replace($filename));
        }
    }

    protected function replace(string $filename): void
    {
        $content = $this->open($filename);

        $content = $this->replaceCommandNames($content);

        $this->store($filename, $content);
    }

    protected function open(string $filename): string
    {
        return file_get_contents($this->basePath($filename));
    }

    protected function store(string $filename, string $content): void
    {
        File::store($this->basePath($filename), $content);
    }

    protected function replaceCommandNames(string $content): string
    {
        return Str::of($content)->replace(
            [
                'make:migration:action',
                'migrate:actions',
                'migrate:actions:fresh',
                'migrate:actions:install',
                'migrate:actions:refresh',
                'migrate:actions:reset',
                'migrate:actions:rollback',
                'migrate:actions:status',
                'migrate:actions:upgrade',
            ],
            [
                Names::MAKE,
                Names::ACTIONS,
                Names::FRESH,
                Names::INSTALL,
                Names::REFRESH,
                Names::RESET,
                Names::ROLLBACK,
                Names::STATUS,
                Names::UPGRADE,
            ],
        )->toString();
    }

    protected function getProjectFiles(): array
    {
        return $this->getFiles(
            base_path(),
            fn (string $path) => Str::endsWith($path, '.php')
                && ! Str::startsWith(realpath($path), [
                    realpath(base_path('vendor')),
                    realpath(base_path('node_modules')),
                ])
        );
    }

    protected function basePath(?string $filename = null): string
    {
        return base_path($filename);
    }
}
