<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Processors;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

use function base_path;
use function date;
use function Laravel\Prompts\text;
use function realpath;

class MakeProcessor extends Processor
{
    protected string $fallback = 'auto';

    protected string $defaultStub = __DIR__ . '/../../resources/stubs/deploy-operation.stub';

    public function handle(): void
    {
        $fullPath = $this->getFullPath();

        $this->notification->task($this->message($fullPath), fn () => $this->create($fullPath));
    }

    protected function message(string $path): string
    {
        return 'Operation [' . $this->displayName($path) . '] created successfully';
    }

    protected function create(string $path): void
    {
        $this->file->ensureDirectoryExists($this->file->dirname($path), 0o755);
        $this->file->copy($this->stubPath(), $path);
    }

    protected function displayName(string $path): string
    {
        return Str::of($path)
            ->when(! $this->showFullPath(), fn (Stringable $str) => $str->after(base_path()))
            ->replace('\\', '/')
            ->ltrim('./')
            ->toString();
    }

    protected function getName(): string
    {
        return $this->getFilename(
            $this->getBranchName()
        );
    }

    protected function getPath(): string
    {
        return $this->options->path;
    }

    protected function getFullPath(): string
    {
        return $this->getPath() . $this->getName();
    }

    protected function getFilename(string $branch): string
    {
        $directory = $this->file->dirname($branch);
        $filename  = $this->file->name($branch);

        return Str::of($filename)
            ->snake()
            ->prepend($this->getTime())
            ->finish('.php')
            ->prepend($directory . '/')
            ->replace('\\', '/')
            ->ltrim('./')
            ->toString();
    }

    protected function getBranchName(): string
    {
        if ($name = trim((string) $this->options->name)) {
            return $name;
        }

        if ($name = $this->askForName()) {
            return $name;
        }

        return $this->git->currentBranch() ?? $this->fallback;
    }

    protected function askForName(): string
    {
        $prompt = $this->promptForName();

        return text($prompt[0], $prompt[1], hint: $prompt[2]);
    }

    protected function promptForName(): array
    {
        return ['What should the operation be named?', 'E.g. activate articles', 'Press Enter to autodetect'];
    }

    protected function getTime(): string
    {
        return date('Y_m_d_His_');
    }

    protected function stubPath(): string
    {
        if ($path = realpath(base_path('stubs/deploy-operation.stub'))) {
            return $path;
        }

        return $this->defaultStub;
    }

    protected function showFullPath(): bool
    {
        return $this->config->show->fullPath;
    }
}
