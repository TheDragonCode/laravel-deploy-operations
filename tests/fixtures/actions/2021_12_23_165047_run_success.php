<?php

use DragonCode\LaravelActions\Support\Actionable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class RunSuccess extends Actionable
{
    public function up(): void
    {
        $this->table()->insert([
            'value' => 'foo',
        ]);
    }

    public function down(): void
    {
        $this->table()->insert([
            'value' => 'bar',
        ]);
    }

    public function success(): void
    {
        $this->table()->insert([
            'value' => 'success',
        ]);
    }

    protected function table(): Builder
    {
        return DB::table('success');
    }
}
