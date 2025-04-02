<?php

declare(strict_types=1);

use DragonCode\LaravelDeployOperations\Operation;

return new class extends Operation {
    public function __invoke()
    {
        // some
    }

    public function shouldRun(): bool
    {
        return app()->isProduction();
    }
};
