<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\ServiceProvider;
use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Helpers\Str;
use DragonCode\Support\Helpers\Ables\Stringable;

class Upgrade extends Processor
{
    public function handle(): void
    {
        if ($this->alreadyUpgraded()) {
            $this->notification->info('Action upgrade already done');

            return;
        }

        $this->run();
    }

    protected function run(): void
    {
        $this->moveFiles();
        $this->moveConfig();
    }

    protected function moveFiles(): void
    {
        foreach ($this->getOldFiles() as $filename) {
            $this->notification->task($filename, fn () => $this->move($filename));
        }
    }

    protected function move(string $filename): void
    {
        $content = $this->open($filename);

        $content = $this->replaceNamespace($content);
        $content = $this->replaceClassName($content);
        $content = $this->replaceDeclareStrictType($content);
        $content = $this->replaceWithInvoke($content);

        $this->store($filename, $content);
        $this->delete($filename);
    }

    protected function open(string $path): string
    {
        return file_get_contents(base_path('database/actions/' . $path));
    }

    protected function store(string $path, string $content): void
    {
        file_put_contents($this->config->path($path), $content);
    }

    protected function delete(string $path): void
    {
        File::ensureDelete($this->config->path($path));
    }

    protected function replaceNamespace(string $content): string
    {
        return Str::of($content)->replace(
            ['DragonCode\\LaravelActions\\Support\\Actionable', 'Actionable'],
            ['DragonCode\\LaravelActions\\Action', 'Action']
        )->toString();
    }

    protected function replaceClassName(string $content): string
    {
        return Str::of($content)
            ->pregReplace('/^([final\s|class]+.+extends\sAction)$/', 'return new class () extends Action')
            ->trim()
            ->append(';')
            ->append(PHP_EOL)
            ->toString();
    }

    protected function replaceDeclareStrictType(string $content): string
    {
        return Str::of($content)
            ->replace('(declare\s*\(\s*strict_types\s*=\s*[1|0]\);)', '')
            ->replace("<?php\n", "<?php\n\ndeclare(strict_types=1);\n")
            ->toString();
    }

    protected function replaceWithInvoke(string $content): string
    {
        return Str::of($content)
            ->when(! Str::matchContains($content, '/public\s+function\s+down/'), function (Stringable $string) {
                return $string->pregReplace('/(public\s+function\s+up)/', 'public function __invoke');
            })->toString();
    }

    protected function moveConfig(): void
    {
        $this->artisan('vendor:publish', [
            '--provider' => ServiceProvider::class,
            '--force'    => true,
        ]);

        $path = config_path('actions.php');

        $table = config('database.actions', 'migration_actions');

        $content = Str::replace(file_get_contents($path), "'table' => 'migration_actions'", "'table' => '$table'");

        file_put_contents($path, $content);
    }

    protected function getOldFiles(): array
    {
        return $this->getFiles(path: base_path('database/actions'));
    }

    protected function alreadyUpgraded(): bool
    {
        return Directory::exists($this->config->path());
    }
}
