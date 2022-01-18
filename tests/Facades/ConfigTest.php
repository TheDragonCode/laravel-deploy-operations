<?php

declare(strict_types=1);

namespace Tests\Facades;

use DragonCode\LaravelActions\Facades\Config;
use Tests\TestCase;

class ConfigTest extends TestCase
{
    public function testEnabled()
    {
        $this->setCache(true);
        $this->setRestart(true);

        $this->assertTrue(Config::cache()->config);
        $this->assertTrue(Config::cache()->route);
        $this->assertTrue(Config::cache()->view);
        $this->assertTrue(Config::cache()->event);

        $this->assertTrue(Config::queue()->queue);
        $this->assertTrue(Config::queue()->horizon);
        $this->assertTrue(Config::queue()->octane);
    }

    public function testDisabled()
    {
        $this->setCache(false);
        $this->setRestart(false);

        $this->assertFalse(Config::cache()->config);
        $this->assertFalse(Config::cache()->route);
        $this->assertFalse(Config::cache()->view);
        $this->assertFalse(Config::cache()->event);

        $this->assertFalse(Config::queue()->queue);
        $this->assertFalse(Config::queue()->horizon);
        $this->assertFalse(Config::queue()->octane);
    }
}
