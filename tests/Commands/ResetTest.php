<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use Tests\TestCase;

class ResetTest extends TestCase
{
    public function testResetCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->run();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::MAKE, ['name' => 'Reset'])->run();
        $this->artisan(Names::MIGRATE)->run();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationHas($this->table, 'reset');

        $this->artisan(Names::RESET)->run();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'reset');
    }
}
