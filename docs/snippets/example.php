<?php

declare(strict_types=1);

use App\Models\Article;
use DragonCode\LaravelDeployOperations\Operation;

return new class extends Operation {
    public function __invoke(): void
    {
        Article::query()
            ->lazyById(chunkSize: 100, column: 'id')
            ->each->update(['is_active' => true]);
        // and/or any actions...
    }
};
