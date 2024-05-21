<?php

namespace Tests\Commands;

use DragonCode\LaravelDeployOperations\Constants\Names;
use Tests\TestCase;

class RefreshTest extends TestCase
{
    public function testRefreshCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::Make, ['name' => 'Refresh'])->assertExitCode(0);
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 1);

        $this->artisan(Names::Refresh)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseOperationHas($this->table, 'refresh');
    }
}
