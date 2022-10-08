<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use Tests\TestCase;

class InstallTest extends TestCase
{
    public function testCreate(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)
            ->expectsOutputToContain('Installing the action repository')
            ->assertSuccessful();

        $this->assertDatabaseHasTable($this->table);
    }

    public function testAlreadyCreated(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)
            ->expectsOutputToContain('Installing the action repository')
            ->assertSuccessful();

        $this->artisan(Names::INSTALL)
            ->expectsOutputToContain('Actions repository already exists')
            ->assertSuccessful();

        $this->assertDatabaseHasTable($this->table);
    }
}
