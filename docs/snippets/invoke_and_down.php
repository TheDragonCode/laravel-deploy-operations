<?php

declare(strict_types=1);

use DragonCode\LaravelDeployOperations\Operation;

return new class extends Operation {
    // called when `php artisan operations` running
    public function __invoke(): void {}

    // doesn't call when `php artisan migrate:rollback` running
    // and any other commands to revert the operation.
    public function down(): void {}
};
