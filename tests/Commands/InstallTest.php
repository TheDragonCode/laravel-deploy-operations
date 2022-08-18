<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use Tests\TestCase;

class InstallTest extends TestCase
{
    public function testNotFound(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::STATUS)->expectsOutput('Actions table not found.');
    }

    public function testCreate(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->run();

        $this->assertDatabaseHasTable($this->table);
    }
}
