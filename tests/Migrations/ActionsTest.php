<?php

declare(strict_types=1);

namespace Tests\Migrations;

use DragonCode\LaravelActions\Constants\Names;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ActionsTest extends TestCase
{
    public function testRunActionsAfterInstall(): void
    {
        Schema::dropAllTables();

        $this->artisan('migrate:install')->run();

        $this->assertDatabaseCount('migrations', 0);
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->run();

        $this->assertDatabaseCount('migrations', 0);
        $this->assertDatabaseHasTable($this->table);

        $this->artisan('migrate')->run();

        $this->assertDatabaseCount('migrations', 9);

        $this->assertDatabaseHas('migrations', [
            'migration' => '2022_08_18_180137_change_migration_actions_table',
        ]);

        $this->assertDatabaseHas('migrations', [
            'migration' => '2023_01_21_172923_rename_migrations_actions_table_to_actions',
        ]);
    }
}
