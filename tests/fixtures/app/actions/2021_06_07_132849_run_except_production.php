<?php

use DragonCode\LaravelActions\Services\Actionable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

return new class () extends Actionable
{
    protected $except_environment = 'production';

    public function up(): void
    {
        $this->table()->insert([
            'value' => Uuid::uuid4(),
        ]);
    }

    public function down(): void
    {
        $this->table()->insert([
            'value' => Uuid::uuid4(),
        ]);
    }

    protected function table(): Builder
    {
        return DB::table('environment');
    }
};
