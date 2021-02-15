<?php

use Helldar\LaravelActions\Support\Actionable;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

final class EveryTime extends Actionable
{
    protected $once = false;

    public function up(): void
    {
        $this->table()->insert([
            'value' => Uuid::uuid4(),
        ]);
    }

    protected function table()
    {
        return DB::table('every_time');
    }
}
