<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Repositories;

use DragonCode\LaravelActions\Helpers\Config;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Query\Builder as Query;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

class ActionRepository
{
    public function __construct(
        protected Resolver $resolver,
        protected Config   $config
    ) {
    }

    public function getCompleted(): array
    {
        return $this->getOrderTable()
            ->pluck('action')
            ->all();
    }

    public function getByStep(?int $steps = null): array
    {
        return $this->getOrderTable('desc')
            ->when($steps, fn (Query $builder) => $builder->take($steps))
            ->get()
            ->all();
    }

    public function getLast(): array
    {
        return $this->getOrderTable('desc')
            ->where('batch', $this->getLastBatchNumber())
            ->get()
            ->all();
    }

    public function getNextBatchNumber(): int
    {
        return $this->getLastBatchNumber() + 1;
    }

    public function getLastBatchNumber(): int
    {
        return (int) $this->table()->max('batch');
    }

    public function log(string $action, int $batch): void
    {
        $this->table()->insert(compact('action', 'batch'));
    }

    public function delete(string $action): void
    {
        $this->table()->where(compact('action'))->delete();
    }

    public function createRepository(): void
    {
        $this->schema()->create($this->config->table(), function (Blueprint $table) {
            $table->id();

            $table->string('action');

            $table->unsignedInteger('batch');
        });
    }

    public function repositoryExists(): bool
    {
        return $this->schema()->hasTable($this->config->table());
    }

    public function deleteRepository(): void
    {
        $this->schema()->dropIfExists($this->config->table());
    }

    protected function getOrderTable(string $order = 'asc'): Query
    {
        return $this->table()
            ->orderBy('batch', $order)
            ->orderBy('action', $order);
    }

    protected function schema(): Builder
    {
        return $this->getConnection()->getSchemaBuilder();
    }

    protected function getConnection(): ConnectionInterface
    {
        return $this->resolver->connection(
            $this->config->connection()
        );
    }

    protected function table(): Query
    {
        return $this->getConnection()->table($this->config->table())->useWritePdo();
    }
}
