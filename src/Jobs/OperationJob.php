<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Jobs;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Constants\Options;
use DragonCode\LaravelDeployOperations\Data\Config\ConfigData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

use function app;

class OperationJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $filename,
    ) {
        $this->onConnection($this->config()->queue->connection);
        $this->onQueue($this->config()->queue->name);
    }

    public function handle(): void
    {
        Artisan::call(Names::Operations, [
            '--' . Options::Path => $this->filename,
            '--' . Options::Sync => true,
        ]);
    }

    public function uniqueId(): string
    {
        return $this->filename;
    }

    protected function config(): ConfigData
    {
        return app(ConfigData::class);
    }
}
