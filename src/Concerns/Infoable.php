<?php

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelActions\Facades\Information;
use Symfony\Component\Console\Output\OutputInterface;

trait Infoable
{
    /**
     * Write a string as standard output.
     *
     * @param  string  $string
     * @param  string|null  $style
     * @param  int|string|null  $verbosity
     */
    public function line($string, $style = null, $verbosity = null)
    {
        if ($this->output) {
            $string = Information::replace($string);

            $styled = $style ? "<$style>$string</$style>" : $string;

            $this->output->writeln($styled, $this->parsedVerbosity($verbosity));
        }
    }

    protected function note($message)
    {
        $this->line($message);
    }

    protected function parsedVerbosity($verbosity = null)
    {
        return method_exists($this, 'parseVerbosity')
            ? $this->parseVerbosity($verbosity)
            : OutputInterface::VERBOSITY_NORMAL;
    }
}
