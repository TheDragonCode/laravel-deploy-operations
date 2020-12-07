<?php

namespace Tests\Commands;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class InstallTest extends TestCase
{
    public function testRepositoryNotFound()
    {
        $this->assertFalse(
            Schema::hasTable($this->table)
        );

        $this->artisan('migrate:actions:status')
            ->expectsOutput('Migration table not found.');
    }

    public function testRepository()
    {
        $this->assertFalse(
            Schema::hasTable($this->table)
        );

        $this->artisan('migrate:actions:install')->run();

        $this->assertTrue(
            Schema::hasTable($this->table)
        );
    }
}
