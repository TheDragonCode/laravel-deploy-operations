<?php

declare(strict_types=1);

namespace Tests;

use DragonCode\LaravelDeployOperations\Repositories\OperationsRepository;
use DragonCode\LaravelDeployOperations\ServiceProvider;
use Illuminate\Container\Container;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\Concerns\AssertDatabase;
use Tests\Concerns\Database;
use Tests\Concerns\Files;

abstract class TestCase extends BaseTestCase
{
    use AssertDatabase;
    use Database;
    use Files;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->freshDatabase();
        $this->freshFiles();
    }

    protected function getPackageProviders($app): array
    {
        return [ServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $this->setDatabase($app);
    }

    protected function table(): Builder
    {
        return DB::table($this->table);
    }

    protected function repository(): OperationsRepository
    {
        return Container::getInstance()->make(OperationsRepository::class);
    }
}
