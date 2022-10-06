<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use Tests\TestCase;

class InstallTest extends TestCase
{
    public function testNotFound(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::STATUS)
            ->expectsOutputToContain('Actions table not found.')
            ->assertSuccessful();

        $this->assertDatabaseDoesntTable($this->table);
    }

    public function testCreate(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->assertSuccessful();

        $this->assertDatabaseHasTable($this->table);
    }
}
