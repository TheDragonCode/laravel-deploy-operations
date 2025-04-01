<?php

declare(strict_types=1);

namespace Tests\Commands;

use DragonCode\LaravelDeployOperations\Constants\Names;
use Tests\TestCase;

class ResetTest extends TestCase
{
    public function testResetCommand(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::Make, ['name' => 'Reset'])->assertExitCode(0);
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseOperationHas($this->table, 'reset');

        $this->artisan(Names::Reset)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'reset');
    }
}
