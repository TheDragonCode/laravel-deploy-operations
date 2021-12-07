<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions;

use DragonCode\LaravelActions\Dumper\Schemas\MySql;
use DragonCode\LaravelActions\Dumper\Schemas\Postgres;
use DragonCode\LaravelActions\Dumper\Schemas\SQLite;
use Illuminate\Database\Schema\MySqlSchemaState;
use Illuminate\Database\Schema\PostgresSchemaState;
use Illuminate\Database\Schema\SqliteSchemaState;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class DumperServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(MySqlSchemaState::class, MySql::class);
        $this->app->bind(PostgresSchemaState::class, Postgres::class);
        $this->app->bind(SqliteSchemaState::class, SQLite::class);
    }
}
