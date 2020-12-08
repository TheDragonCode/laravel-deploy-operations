<?php

namespace Tests\Commands;

use InvalidArgumentException;
use Tests\TestCase;

final class CreatorTest extends TestCase
{
    public function testCreateAction()
    {
        $name = 'FooExample';

        $filename = date('Y_m_d_His') . '_foo_example.php';

        $path = database_path('actions/' . $filename);

        $this->assertFileDoesNotExist($path);

        $this->artisan('make:migration:action', compact('name'))->run();

        $this->assertFileExists($path);
    }

    public function testAlreadyExists()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A BarExample class already exists.');

        $name = 'BarExample';

        $this->artisan('make:migration:action', compact('name'))->run();
        $this->artisan('make:migration:action', compact('name'))->run();
    }
}
