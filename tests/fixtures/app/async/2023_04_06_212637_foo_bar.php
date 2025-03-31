<?php

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
        return DB::table('test');
    }

    public function shouldBeAsync(): bool
    {
        return true;
    }
};
