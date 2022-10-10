<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Repositories;

use DragonCode\LaravelActions\Constants\Order;
use DragonCode\LaravelActions\Helpers\Config;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Query\Builder as Query;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Collection;

class ActionRepository
{
    protected ?string $connection = null;

    public function __construct(
        protected Resolver $resolver,
        protected Config   $config
    ) {
    }

    public function setConnection(?string $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    public function getCompleted(): Collection
    {
        return $this->sortedTable()->get();
    }

    public function getByStep(int $steps): array
    {
        return $this->sortedTable(Order::DESC)
            ->whereIn('batch', $this->getBatchNumbers($steps))
            ->get()
            ->all();
    }

    public function getLast(): array
    {
        return $this->sortedTable(Order::DESC)
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
            $table->bigIncrements('id');

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

    /**
     * @param int $steps
     *
     * @return array<int>
     */
    protected function getBatchNumbers(int $steps): array
    {
        return $this->sortedTable(Order::DESC)
            ->pluck('batch')
            ->unique()
            ->take($steps)
            ->all();
    }

    protected function sortedTable(string $order = Order::ASC): Query
    {
        return $this->table()
            ->orderBy('batch', $order)
            ->orderBy('id', $order);
    }

    protected function schema(): Builder
    {
        return $this->getConnection()->getSchemaBuilder();
    }

    protected function table(): Query
    {
        return $this->getConnection()->table($this->config->table())->useWritePdo();
    }

    protected function getConnection(): ConnectionInterface
    {
        return $this->resolver->connection(
            $this->connection ?: $this->config->connection()
        );
    }
}
