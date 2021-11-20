<?php

namespace DragonCode\LaravelActions\Contracts;

interface Actionable
{
    /**
     * Run the actions.
     */
    public function up(): void;

    /**
     * Reverse the actions.
     */
    public function down(): void;
}
