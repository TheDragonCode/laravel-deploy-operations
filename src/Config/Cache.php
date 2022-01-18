<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Config;

use DragonCode\SimpleDataTransferObject\DataTransferObject;

class Cache extends DataTransferObject
{
    public $config = false;

    public $route = false;

    public $view = false;

    public $event = false;
}
