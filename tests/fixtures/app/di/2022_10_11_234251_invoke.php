<?php

declare(strict_types=1);

use DragonCode\LaravelDeployOperations\Operation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\Some;

return new class extends Operation {
    public function __invoke(Some $some): void
    {
        $this->table()->insert([
            'value' => $some->get('invoke'),
        ]);
    }

    protected function table(): Builder
    {
        return DB::table('test');
    }
};
