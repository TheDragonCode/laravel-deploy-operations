<?php

declare(strict_types=1);

use DragonCode\LaravelDeployOperations\Helpers\ConfigHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('actions') && $this->doesntSame('actions', $this->table())) {
            $this->validateTable($this->table());

            Schema::rename('actions', $this->table());
        }
    }

    public function down(): void
    {
        if (Schema::hasTable($this->table()) && $this->doesntSame('actions', $this->table())) {
            $this->validateTable('actions');

            Schema::rename($this->table(), 'actions');
        }
    }

    protected function validateTable(string $name): void
    {
        if (Schema::hasTable($name)) {
            throw new RuntimeException(sprintf('A table named [%s] already exists. Change the table name settings in the [%s] configuration file.', $name, 'config/deploy-operations.php'));
        }
    }

    protected function doesntSame(string $first, string $second): bool
    {
        return $first !== $second;
    }

    protected function table(): string
    {
        return app(ConfigHelper::class)->table();
    }
};
