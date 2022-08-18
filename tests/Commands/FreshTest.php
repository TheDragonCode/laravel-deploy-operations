<?php

declare(strict_types=1);

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use Tests\TestCase;

class FreshTest extends TestCase
{
    public function testFreshCommand(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->run();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::MAKE, ['name' => 'Fresh'])->run();
        $this->artisan(Names::MIGRATE)->run();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 1);

        $this->artisan(Names::FRESH)->run();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationHas($this->table, 'fresh');
    }
}
