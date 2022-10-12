<?php

use DragonCode\LaravelActions\Action;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\Some;

return new class () extends Action
{
    public function up(Some $some): void
    {
        $this->table()->insert([
            'value' => $some->get('up_down'),
        ]);
    }

    public function down(Some $some): void
    {
        $this->table()
            ->where('value', $some->get('up_down'))
            ->delete();
    }

    protected function table(): Builder
    {
        return DB::table('test');
    }
};
