<?php

use DragonCode\LaravelActions\Action;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

return new class extends Action {
    protected bool $once = false;

    protected bool $async = true;

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
};
