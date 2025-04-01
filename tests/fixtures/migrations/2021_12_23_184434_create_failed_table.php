<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('failed', function (Blueprint $table) {
            $table->string('value');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed');
    }
};
