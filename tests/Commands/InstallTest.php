<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use Tests\TestCase;

class InstallTest extends TestCase
{
    public function testCreate(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
    }

    public function testAlreadyCreated(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->assertExitCode(0);
        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
    }

    public function testMutedCreate(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL, ['--' . Options::MUTE => true])->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
    }

    public function testMutedAlreadyCreated(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL, ['--' . Options::MUTE => true])->assertExitCode(0);
        $this->artisan(Names::INSTALL, ['--' . Options::MUTE => true])->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
    }
}
