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
        $this->clean();
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
        $content = $this->replaceProperties($content);

        $this->store($filename, $content);
    }

    protected function clean(): void
    {
        $this->notification->task('Delete old directory', fn () => Directory::ensureDelete(
            database_path('actions')
        ));
    }

    protected function open(string $filename): string
    {
        return file_get_contents(base_path('database/actions/' . $filename));
    }

    protected function store(string $filename, string $content): void
    {
        File::store($this->config->path($filename), $content);
    }

    protected function delete(string $filename): void
    {
        File::ensureDelete($filename);
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
            ->pregReplace('/((?:return\s+new\s+class\s*\(?\s*\)?|final\s+class|class)\s*.+extends\s+Action)/', 'return new class () extends Action')
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

    protected function replaceProperties(string $content): string
    {
        return Str::of($content)
            ->pregReplace('/protected\s+\$once/', 'protected bool $once')
            ->pregReplace('/protected\s+\$transactions/', 'protected bool $transactions')
            ->pregReplace('/protected\s+\$transaction_attempts/', 'protected int $transactionAttempts')
            ->pregReplace('/protected\s+\$environment/', 'protected string|array|null $environment')
            ->pregReplace('/protected\s+\$except_environment/', 'protected string|array|null $exceptEnvironment')
            ->pregReplace('/protected\s+\$before/', 'protected bool $before')
            ->toString();
    }

    protected function moveConfig(): void
    {
        $this->notification->task('Moving config file', function () {
            $this->artisan('vendor:publish', [
                '--provider' => ServiceProvider::class,
                '--force'    => true,
            ]);

            $path = config_path('actions.php');

            $table = config('database.actions', 'migration_actions');

            $content = Str::replace(file_get_contents($path), "'table' => 'migration_actions'", "'table' => '$table'");

            file_put_contents($path, $content);
        });
    }

    protected function getOldFiles(): array
    {
        return $this->getFiles(path: database_path('actions'), realpath: true);
    }

    protected function alreadyUpgraded(): bool
    {
        return Directory::exists($this->config->path());
    }
}
