<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Database;

use DragonCode\LaravelActions\Action;
use DragonCode\LaravelActions\Helpers\Config;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class BaseChangeMigrationColumn extends Action
{
    public function __construct(
        protected Config $config
    ) {
    }

    public function up(): void
    {
        if ($this->hasTable()) {
            Schema::table($this->table(), function (Blueprint $table) {
                $table->renameColumn('migration', 'action');

                $table->unsignedInteger('batch')->change();
            });
        }
    }

    public function down(): void
    {
        if ($this->hasTable()) {
            Schema::table($this->table(), function (Blueprint $table) {
                $table->renameColumn('action', 'migration');

                $table->integer('batch')->change();
            });
        }
    }

    protected function hasTable(): bool
    {
        return Schema::hasTable($this->table());
    }

    protected function table(): string
    {
        return $this->config->table();
    }
}
