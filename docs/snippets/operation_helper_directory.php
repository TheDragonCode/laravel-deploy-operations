<?php

declare(strict_types=1);

use DragonCode\LaravelDeployOperations\Helpers\OperationHelper;

OperationHelper::run('foo');

// also you can use a real path
OperationHelper::run(__DIR__ . '/../foo', realpath: true);
