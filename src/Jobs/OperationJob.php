<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Jobs;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Constants\Options;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

use function config;

class OperationJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $filename
    ) {
        $this->setQueueConnection();
        $this->setQueueName();
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

    protected function setQueueConnection(): void
    {
        $this->onConnection(config('deploy-operations.queue.connection'));
    }

    protected function setQueueName(): void
    {
        $this->onQueue(config('deploy-operations.queue.name'));
    }
}
