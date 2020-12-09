<?php

namespace Tests\Commands;

use Tests\TestCase;

final class InstallTest extends TestCase
{
    public function testRepositoryNotFound()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan('migrate:actions:status')
            ->expectsOutput('Actions table not found.');
    }

    public function testRepository()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan('migrate:actions:install')->run();

        $this->assertDatabaseHasTable($this->table);
    }
}
