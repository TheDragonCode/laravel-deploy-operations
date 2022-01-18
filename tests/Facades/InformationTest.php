<?php

declare(strict_types=1);

namespace Tests\Facades;

use DragonCode\LaravelActions\Facades\Information;
use Tests\TestCase;

class InformationTest extends TestCase
{
    public function testMigrated()
    {
        $this->assertSame('Done: foo', Information::replace('Migrated: foo'));
        $this->assertSame('Migrated foo', Information::replace('Migrated foo'));
    }

    public function testMigrating()
    {
        $this->assertSame('Running: foo', Information::replace('Migrating: foo'));
        $this->assertSame('Migrating foo', Information::replace('Migrating foo'));
    }

    public function testMigrationNotFound()
    {
        $this->assertSame('Action not found: foo', Information::replace('Migration not found: foo'));
        $this->assertSame('Action not found foo', Information::replace('Migration not found foo'));
        $this->assertSame('Action not found', Information::replace('Migration not found'));
    }

    public function testCreatedSuccessfully()
    {
        $this->assertSame('Action table created successfully: foo', Information::replace('Migration table created successfully: foo'));
        $this->assertSame('Action table created successfully foo', Information::replace('Migration table created successfully foo'));
        $this->assertSame('Action table created successfully', Information::replace('Migration table created successfully'));
    }

    public function testTableNotFound()
    {
        $this->assertSame('Actions table not found: foo', Information::replace('Migration table not found: foo'));
        $this->assertSame('Actions table not found foo', Information::replace('Migration table not found foo'));
        $this->assertSame('Actions table not found', Information::replace('Migration table not found'));
    }

    public function testNotFound()
    {
        $this->assertSame('No actions found: foo', Information::replace('No migrations found: foo'));
        $this->assertSame('No actions found foo', Information::replace('No migrations found foo'));
        $this->assertSame('No actions found', Information::replace('No migrations found'));
    }

    public function testNothing()
    {
        $this->assertSame('Nothing to do: foo', Information::replace('Nothing to migrate: foo'));
        $this->assertSame('Nothing to do foo', Information::replace('Nothing to migrate foo'));
        $this->assertSame('Nothing to do', Information::replace('Nothing to migrate'));
    }

    public function testCustom()
    {
        $this->assertSame('qwerty', Information::replace('qwerty'));
    }
}
