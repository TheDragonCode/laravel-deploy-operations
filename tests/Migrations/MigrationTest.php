<?php

declare(strict_types=1);

namespace Tests\Migrations;

use DragonCode\LaravelActions\Constants\Names;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationTest extends TestCase
{
    public function testRunMigrationAfterInstall(): void
    {
        DB::table('migrations')->truncate();

        Schema::connection($this->database)->dropIfExists($this->table);

        $this->assertDatabaseCount('migrations', 0);
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->run();

        $this->assertDatabaseCount('migrations', 0);
        $this->assertDatabaseHasTable($this->table);

        $this->artisan('migrate')->run();

        $this->assertDatabaseCount('migrations', 1);
        $this->assertDatabaseHas('migrations', ['migration' => '2022_08_18_180137_change_migration_actions_table']);
    }
}
