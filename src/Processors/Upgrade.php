<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Processors;

use DragonCode\LaravelDeployOperations\ServiceProvider;
use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Helpers\Str;
use Stringable;

class Upgrade extends Processor
{
    public function handle(): void
    {
        if ($this->alreadyUpgraded()) {
            $this->notification->info('Operations upgrade already done');

            return;
        }

        $this->run();
    }

    protected function run(): void
    {
        $this->moveFiles();
        $this->moveConfig();
        $this->moveStub();
        $this->callMigration();
        $this->clean();
    }

    protected function moveFiles(): void
    {
        foreach ($this->getOldFiles() as $filename) {
            $this->notification->task($filename, fn () => $this->move(
                $this->oldPath($filename . '.php'),
                $this->newPath($filename . '.php')
            ));
        }
    }

    protected function move(string $from, string $to): void
    {
        $content = $this->open($from);

        $content = $this->replaceNamespace($content);
        $content = $this->replaceClassName($content);
        $content = $this->replaceDeclareStrictType($content);
        $content = $this->replaceWithInvoke($content);

        $this->store($to, $content);
    }

    protected function clean(): void
    {
        $this->notification->task(
            'Delete old directory',
            fn () => Directory::ensureDelete($this->oldPath())
        );
    }

    protected function open(string $filename): string
    {
        return file_get_contents($filename);
    }

    protected function store(string $filename, string $content): void
    {
        File::store($filename, $content);
    }

    protected function replaceNamespace(string $content): string
    {
        return Str::of($content)->replace(
            ['DragonCode\\LaravelActions\\Action', 'Action'],
            ['DragonCode\\LaravelDeployOperations\\Operation', 'Operation'],
        )->toString();
    }

    protected function replaceClassName(string $content): string
    {
        return Str::of($content)
            ->pregReplace(
                '/((?:return\s+new\s+class\s*\(?\s*\)?|final\s+class|class)\s*.+extends\s+Action)/',
                'return new class extends Operation'
            )
            ->trim()
            ->trim(';')
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
        $this->notification->task('Moving config file', function () {
            $this->runCommand('vendor:publish', [
                '--provider' => ServiceProvider::class,
                '--force'    => true,
            ]);

            $path = config_path('deploy-operations.php');

            $table = config('actions.table', 'operations');

            $content = Str::replace(file_get_contents($path), "'table' => 'operations'", "'table' => '$table'");

            file_put_contents($path, $content);

            File::ensureDelete(config_path('actions.php'));
        });
    }

    protected function moveStub(): void
    {
        if (! File::exists(base_path('stubs/action.stub'))) {
            $this->notification->info('Stub file doesn\'t exist.');

            return;
        }

        $this->notification->task('Moving stub file', fn () => $this->move(
            base_path('stubs/action.stub'),
            base_path('stubs/deploy-operation.stub'),
        ));
    }

    protected function callMigration(): void
    {
        $this->notification->task('Call migrations', function () {
            $this->runCommand('migrate', [
                '--path'     => __DIR__ . '/../../database/migrations/2024_05_21_112438_rename_actions_table_to_operations.php',
                '--realpath' => true,
                '--force'    => true,
            ]);

            $this->runCommand('migrate', [
                '--path'     => __DIR__ . '/../../database/migrations/2024_05_21_114318_rename_column_in_operations_table',
                '--realpath' => true,
                '--force'    => true,
            ]);
        });
    }

    protected function getOldFiles(): array
    {
        return $this->getFiles($this->oldPath());
    }

    protected function oldPath(?string $filename = null): string
    {
        return base_path('actions/' . $filename);
    }

    protected function newPath(?string $filename = null): string
    {
        return $this->options->path . '/' . $filename;
    }

    protected function alreadyUpgraded(): bool
    {
        return Directory::exists($this->newPath());
    }
}
