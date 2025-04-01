<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('every_time', function (Blueprint $table) {
            $table->uuid('value');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('every_time');
    }
};
