<?php

declare(strict_types=1);

use DragonCode\LaravelDeployOperations\Operation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

return new class extends Operation {
    public function __invoke(): void
    {
        $this->table()->insert([
            'value' => Uuid::uuid4(),
        ]);
    }

    protected function table(): Builder
    {
        return DB::table('every_time');
    }

    public function shouldOnce(): bool
    {
        return false;
    }

    public function shouldBeAsync(): bool
    {
        return true;
    }
};
