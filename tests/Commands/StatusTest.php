<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use Tests\TestCase;

class StatusTest extends TestCase
{
    public function testStatusCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->run();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::STATUS)->expectsTable([], [])->run();

        $this->artisan(Names::MAKE, ['name' => 'Status'])->run();
        $this->artisan(Names::MIGRATE)->run();

        $this->assertDatabaseCount($this->table, 1);

        $this->artisan(Names::STATUS)->run();

        $this->assertDatabaseMigrationHas($this->table, 'status');
    }
}
