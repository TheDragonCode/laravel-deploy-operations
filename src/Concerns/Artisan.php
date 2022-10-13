<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Concerns;

use Illuminate\Support\Facades\Artisan as Command;
use Symfony\Component\Console\Output\OutputInterface;

trait Artisan
{
    /**
     * Run an Artisan console command by name.
     *
     * @param string $command
     * @param array $parameters
     * @param \Symfony\Component\Console\Output\OutputInterface|null $outputBuffer
     *
     * @return void
     */
    protected function artisan(string $command, array $parameters = [], ?OutputInterface $outputBuffer = null): void
    {
        Command::call($command, $parameters, $outputBuffer);
    }
}
