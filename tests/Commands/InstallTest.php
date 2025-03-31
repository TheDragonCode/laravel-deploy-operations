<?php

declare(strict_types=1);

namespace Tests\Commands;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Constants\Options;
use Tests\TestCase;

class InstallTest extends TestCase
{
    public function testCreate(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
    }

    public function testAlreadyCreated(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::Install)->assertExitCode(0);
        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
    }

    public function testMutedCreate(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::Install, ['--' . Options::Mute => true])->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
    }

    public function testMutedAlreadyCreated(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::Install, ['--' . Options::Mute => true])->assertExitCode(0);
        $this->artisan(Names::Install, ['--' . Options::Mute => true])->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
    }
}
