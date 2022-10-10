<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Console\Command;
use DragonCode\LaravelActions\Constants\Names;
use Tests\TestCase;

class StatusTest extends TestCase
{
    public function testNotFound(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::STATUS)->assertExitCode(Command::SUCCESS);

        $this->assertDatabaseDoesntTable($this->table);
    }

    public function testStatusCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->assertExitCode(Command::SUCCESS);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::STATUS)->expectsTable([], [])->assertExitCode(Command::SUCCESS);

        $this->artisan(Names::MAKE, ['name' => 'Status'])->assertExitCode(Command::SUCCESS);
        $this->artisan(Names::MIGRATE)->assertExitCode(Command::SUCCESS);

        $this->assertDatabaseCount($this->table, 1);

        $this->artisan(Names::STATUS)->assertExitCode(Command::SUCCESS);

        $this->assertDatabaseMigrationHas($this->table, 'status');
    }
}
