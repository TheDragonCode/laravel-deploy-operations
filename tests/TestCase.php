<?php

namespace Tests;

use Helldar\LaravelActions\ServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\Concerns\{Actionable, Database, Files, Settings};

abstract class TestCase extends BaseTestCase
{
    use Actionable;
    use Database;
    use Files;
    use RefreshDatabase;
    use Settings;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->setTable($app);
        $this->setDatabase($app);
    }

    protected function getPackageProviders($app): array
    {
        return [ServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->freshDatabase();
        $this->freshFiles();
    }

    protected function table()
    {
        return DB::table($this->table);
    }
}
