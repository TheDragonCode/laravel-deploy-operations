<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Console\Command;
use DragonCode\LaravelActions\Constants\Names;
use Tests\TestCase;

class RefreshTest extends TestCase
{
    public function testRefreshCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->assertExitCode(Command::SUCCESS);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::MAKE, ['name' => 'Refresh'])->assertExitCode(Command::SUCCESS);
        $this->artisan(Names::MIGRATE)->assertExitCode(Command::SUCCESS);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 1);

        $this->artisan(Names::REFRESH)->assertExitCode(Command::SUCCESS);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationHas($this->table, 'refresh');
    }
}
