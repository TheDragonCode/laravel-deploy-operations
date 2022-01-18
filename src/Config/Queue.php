<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Config;

use DragonCode\SimpleDataTransferObject\DataTransferObject;

class Queue extends DataTransferObject
{
    public $queue = false;

    public $horizon = false;

    public $octane = false;
}
