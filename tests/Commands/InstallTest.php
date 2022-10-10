<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Console\Command;
use DragonCode\LaravelActions\Constants\Names;
use Tests\TestCase;

class InstallTest extends TestCase
{
    public function testCreate(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->assertExitCode(Command::SUCCESS);

        $this->assertDatabaseHasTable($this->table);
    }

    public function testAlreadyCreated(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->assertExitCode(Command::SUCCESS);
        $this->artisan(Names::INSTALL)->assertExitCode(Command::SUCCESS);

        $this->assertDatabaseHasTable($this->table);
    }
}
