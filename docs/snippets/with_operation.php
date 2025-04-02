<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function __invoke()
    {
        // some
    }

    public function withOperation(): string
    {
        return 'foo/2022_10_14_000002_test2';
    }
};
