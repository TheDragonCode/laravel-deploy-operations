<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Repositories;

use DragonCode\LaravelDeployOperations\Constants\Order;
use DragonCode\LaravelDeployOperations\Data\Config\ConfigData;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Query\Builder as Query;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Collection;

use function compact;

class OperationsRepository
{
    protected ?string $connection = null;

    public function __construct(
        protected Resolver $resolver,
        protected ConfigData $config,
    ) {}

    public function getCompleted(): Collection
    {
        return $this->sortedTable()->get();
    }

    public function getByStep(int $steps): array
    {
        return $this->sortedTable(Order::Desc)
            ->whereIn('batch', $this->getBatchNumbers($steps))
            ->get()
            ->all();
    }

    public function getLast(): array
    {
        return $this->sortedTable(Order::Desc)
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

    public function log(string $operation, int $batch): void
    {
        $this->table()->insert(compact('operation', 'batch'));
    }

    public function delete(string $operation): void
    {
        $this->table()->where(compact('operation'))->delete();
    }

    public function createRepository(): void
    {
        $this->schema()->create($this->config->table, function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('operation');

            $table->unsignedInteger('batch');
        });
    }

    public function repositoryExists(): bool
    {
        return $this->schema()->hasTable($this->config->table);
    }

    public function deleteRepository(): void
    {
        $this->schema()->dropIfExists($this->config->table);
    }

    /**
     * @return array<int>
     */
    protected function getBatchNumbers(int $steps): array
    {
        return $this->sortedTable(Order::Desc)
            ->pluck('batch')
            ->unique()
            ->take($steps)
            ->all();
    }

    protected function sortedTable(string $order = Order::Asc): Query
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
        return $this->getConnection()->table($this->config->table)->useWritePdo();
    }

    protected function getConnection(): ConnectionInterface
    {
        return $this->resolver->connection(
            $this->connection ?: $this->config->connection
        );
    }

    public function setConnection(?string $connection): self
    {
        $this->connection = $connection;

        return $this;
    }
}
