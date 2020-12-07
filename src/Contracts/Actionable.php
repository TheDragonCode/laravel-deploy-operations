<?php

namespace Helldar\LaravelActions\Contracts;

interface Actionable
{
    public function up(): void;

    public function down(): void;
}
