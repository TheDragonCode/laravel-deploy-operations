<?php

use DragonCode\LaravelActions\Support\Actionable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class RunSuccessOnFailed extends Actionable
{
    public function up(): void
    {
        throw new Exception('Custom exception');
    }

    public function down(): void
    {
        throw new Exception();
    }

    public function success(): void
    {
        $this->table()->insert([
            'value' => Uuid::uuid4(),
        ]);
    }

    protected function table(): Builder
    {
        return DB::table('success');
    }
}
