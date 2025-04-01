<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void {}

    public function down(): void {}

    public function withOperation(): string
    {
        return '2025_03_31_234251_invoke';
    }
};
