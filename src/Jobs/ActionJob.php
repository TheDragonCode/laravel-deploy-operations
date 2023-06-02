<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Jobs;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class ActionJob implements ShouldQueue, ShouldBeUnique
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
        Artisan::call(Names::ACTIONS, [
            '--' . Options::PATH => $this->filename,
            '--' . Options::SYNC => true,
        ]);
    }

    public function uniqueId(): string
    {
        return $this->filename;
    }

    protected function setQueueConnection(): void
    {
        $this->onConnection(config('actions.queue.connection'));
    }

    protected function setQueueName(): void
    {
        $this->onQueue(config('actions.queue.name'));
    }
}
