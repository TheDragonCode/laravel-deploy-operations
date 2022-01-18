<?php

namespace Tests\Commands;

use InvalidArgumentException;
use Tests\TestCase;

class CreatorTest extends TestCase
{
    public function testCreateAction()
    {
        $name = 'FooExample';

        $filename = date('Y_m_d_His') . '_foo_example.php';

        $path = $this->targetDirectory($filename);

        $this->assertFileDoesNotExist($path);

        $this->artisan('make:migration:action', compact('name'))->run();

        $this->assertFileExists($path);
    }

    public function testDuplicateOnPrev()
    {
        if ($this->isLatestApp()) {
            $this->assertTrue(true);

            return;
        }

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A BarExample class already exists.');

        $name = 'BarExample';

        $this->artisan('make:migration:action', compact('name'))->run();
        $this->artisan('make:migration:action', compact('name'))->run();
    }

    public function testDuplicateOnLatest()
    {
        if ($this->isPrevApp()) {
            $this->assertTrue(true);

            return;
        }

        $name = 'BarExample';

        $time1 = date('Y_m_d_His');
        $this->artisan('make:migration:action', compact('name'))->run();

        $time2 = date('Y_m_d_His');
        $this->artisan('make:migration:action', compact('name'))->run();

        $this->assertFileExists($this->targetDirectory($time1 . '_bar_example.php'));
        $this->assertFileExists($this->targetDirectory($time2 . '_bar_example.php'));
    }
}
