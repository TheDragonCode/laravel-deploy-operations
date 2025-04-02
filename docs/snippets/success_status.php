<?php

declare(strict_types=1);

use DragonCode\LaravelDeployOperations\Operation;
use Illuminate\Support\Facade\Log;

return new class extends Operation {
    public function up(): void
    {
        //
    }

    public function down(): void
    {
        //
    }

    public function success(): void
    {
        Log::info('success');
    }

    public function failed(): void
    {
        Log::info('failed');
    }
};
