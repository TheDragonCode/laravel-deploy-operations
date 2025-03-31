<?php

declare(strict_types=1);

namespace Tests\Commands;

use DragonCode\LaravelDeployOperations\Constants\Names;
use Tests\TestCase;

class StatusTest extends TestCase
{
    public function testNotFound(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::Status)->assertExitCode(0);

        $this->assertDatabaseDoesntTable($this->table);
    }

    public function testStatusCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::Status)->expectsTable([], [])->assertExitCode(0);

        $this->artisan(Names::Make, ['name' => 'Status'])->assertExitCode(0);
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($this->table, 1);

        $this->artisan(Names::Status)->assertExitCode(0);

        $this->assertDatabaseOperationHas($this->table, 'status');
    }
}
