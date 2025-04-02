<?php

declare(strict_types=1);

use DragonCode\LaravelDeployOperations\Operation;
use Tests\Concerns\Some;

return new class extends Operation {
    public function __invoke(Some $some): void
    {
        $value = $some->get('qwerty');
    }
};
