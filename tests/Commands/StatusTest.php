<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use Tests\TestCase;

class StatusTest extends TestCase
{
    public function testNotFound(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::STATUS)->assertExitCode(0);

        $this->assertDatabaseDoesntTable($this->table);
    }

    public function testStatusCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::STATUS)->expectsTable([], [])->assertExitCode(0);

        $this->artisan(Names::MAKE, ['name' => 'Status'])->assertExitCode(0);
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($this->table, 1);

        $this->artisan(Names::STATUS)->assertExitCode(0);

        $this->assertDatabaseMigrationHas($this->table, 'status');
    }
}
