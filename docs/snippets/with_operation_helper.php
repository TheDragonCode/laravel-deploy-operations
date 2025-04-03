<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;

use function DragonCode\LaravelDeployOperations\operation;

return new class extends Migration {
    public function withOperation(): string
    {
        return operation('foo/2022_10_14_000002_test2');
    }
};
