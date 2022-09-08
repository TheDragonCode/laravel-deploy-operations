<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\Concerns\Artisan;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Processor
{
    use Artisan;

    abstract public function handle();

    public function __construct(
        protected array           $options,
        protected InputInterface  $input,
        protected OutputInterface $output
    ) {
    }
}
