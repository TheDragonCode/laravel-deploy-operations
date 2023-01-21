<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Database;

use DragonCode\LaravelActions\Action;
use DragonCode\LaravelActions\Helpers\Config;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class BaseRenameMigrationsActionsTableToActions extends Action
{
    protected Config $config;

    public function __construct()
    {
        $this->config = app(Config::class);
    }

    public function up(): void
    {
        if (Schema::hasTable('migration_actions') && $this->doesntSame('migration_actions', $this->table())) {
            $this->validateTable($this->table());

            Schema::rename('migration_actions', $this->table());
        }
    }

    public function down(): void
    {
        if (Schema::hasTable($this->table()) && $this->doesntSame('migration_actions', $this->table())) {
            $this->validateTable('migration_actions');

            Schema::rename($this->table(), 'migration_actions');
        }
    }

    protected function validateTable(string $name): void
    {
        if (Schema::hasTable($name)) {
            throw new RuntimeException(sprintf(
                    'A table named [%s] already exists. Change the table name settings in the [%s] configuration file.',
                    $name,
                    'config/actions.php'
                )
            );
        }
    }

    protected function doesntSame(string $first, string $second): bool
    {
        return $first !== $second;
    }

    protected function table(): string
    {
        return $this->config->table();
    }
}
