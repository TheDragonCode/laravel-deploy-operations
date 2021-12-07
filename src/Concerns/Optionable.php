<?php

namespace DragonCode\LaravelActions\Concerns;

/** @mixin \Illuminate\Console\Command */
trait Optionable
{
    protected function optionDatabase(): ?string
    {
        return $this->input->getOption('database');
    }

    protected function optionStep(int $default = null): ?int
    {
        return $this->input->getOption('step') ?: $default;
    }
}
