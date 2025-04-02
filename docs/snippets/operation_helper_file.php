<?php

declare(strict_types=1);

use DragonCode\LaravelDeployOperations\Helpers\OperationHelper;

OperationHelper::run('foo/2022_10_14_000002_test2');
// or
OperationHelper::run('foo/2022_10_14_000002_test2.php');

// also you can use a real path
OperationHelper::run(__DIR__ . '/../foo/2022_10_14_000002_test2', realpath: true);
// or
OperationHelper::run(__DIR__ . '/../foo/2022_10_14_000002_test2.php', realpath: true);
