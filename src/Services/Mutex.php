<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Services;

use Carbon\CarbonInterval;
use DragonCode\LaravelActions\Console\Command;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Contracts\Cache\Repository;

class Mutex
{
    protected ?string $store = null;

    public function __construct(
        protected Cache $cache
    ) {}

    public function create(Command $command): bool
    {
        return $this->store()->add($this->name($command), true, $this->ttl());
    }

    public function forget(Command $command): void
    {
        $this->store()->forget(
            $this->name($command)
        );
    }

    protected function store(): Repository
    {
        return $this->cache->store($this->store);
    }

    protected function ttl(): CarbonInterval
    {
        return CarbonInterval::hour();
    }

    protected function name(Command $command): string
    {
        return 'framework' . DIRECTORY_SEPARATOR . 'action-' . $command->getName();
    }
}
