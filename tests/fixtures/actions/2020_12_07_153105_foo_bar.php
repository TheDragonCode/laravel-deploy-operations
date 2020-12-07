<?php

use Helldar\LaravelActions\Support\Actionable;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

final class FooBar extends Actionable
{
    public function up(): void
    {
        $this->table()->insert([
            'value' => Uuid::uuid4(),
        ]);
    }

    public function down(): void
    {
        $this->table()->truncate();
    }

    protected function table()
    {
        return DB::table('test');
    }
}
