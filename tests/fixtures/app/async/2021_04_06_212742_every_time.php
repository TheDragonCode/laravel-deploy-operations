<?php

use DragonCode\LaravelDeployOperations\Operation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

return new class extends Operation {
    protected bool $once = false;

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

    public function isAsync(): bool
    {
        return true;
    }
};
