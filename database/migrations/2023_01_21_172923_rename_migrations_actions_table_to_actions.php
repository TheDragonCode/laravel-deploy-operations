<?php

declare(strict_types=1);

use DragonCode\LaravelDeployOperations\Data\Config\ConfigData;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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
            throw new RuntimeException(sprintf('A table named [%s] already exists. Change the table name settings in the [%s] configuration file.', $name, 'config/actions.php'));
        }
    }

    protected function doesntSame(string $first, string $second): bool
    {
        return $first !== $second;
    }

    protected function table(): string
    {
        return app(ConfigData::class)->table;
    }
};
