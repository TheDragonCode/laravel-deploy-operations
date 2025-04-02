<?php

declare(strict_types=1);

use DragonCode\LaravelDeployOperations\Operation;
use Tests\Concerns\Some;

return new class extends Operation {
    public function up(Some $some): void
    {
        $value = $some->get('qwerty');
    }

    public function down(Some $some): void
    {
        $value = $some->get('qwerty');
    }
};
