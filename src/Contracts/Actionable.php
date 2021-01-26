<?php

namespace Helldar\LaravelActions\Contracts;

interface Actionable
{
    /**
     * Run the actions.
     *
     * @return void
     */
    public function up(): void;

    /**
     * Reverse the actions.
     *
     * @return void
     */
    public function down(): void;
}
