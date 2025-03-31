<?php

declare(strict_types=1);

use DragonCode\LaravelDeployOperations\Helpers\ConfigHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $this->rename('action', 'operation');
    }

    public function down(): void
    {
        $this->rename('operation', 'action');
    }

    protected function rename(string $from, string $to): void
    {
        if (Schema::hasColumn($this->table(), $from)) {
            Schema::table($this->table(), fn (Blueprint $table) => $table->renameColumn($from, $to));
        }
    }

    protected function table(): string
    {
        return app(ConfigHelper::class)->table();
    }
};
