<?php

namespace Tests\Commands;

use Tests\TestCase;

class MakeTest extends TestCase
{
    public function testMakingFiles()
    {
        $name = 'MakeExample';

        $filename = date('Y_m_d_His') . '_make_example.php';

        $path = database_path('actions/' . $filename);

        $this->assertFileDoesNotExist($path);

        $this->artisan('make:migration:action', compact('name'))->run();

        $this->assertFileExists($path);

        $expected = $this->allowAnonymous()
            ? __DIR__ . '/../fixtures/app/anonymous/stubs/make_example.stub'
            : __DIR__ . '/../fixtures/app/named/stubs/make_example.stub';

        $this->assertEquals(file_get_contents($expected), file_get_contents($path));
    }

    public function testAutoName()
    {
        $filename = $this->allowAnonymous()
            ? date('Y_m_d_His') . '_auto.php'
            : date('Y_m_d_His') . '_auto_' . time() . '.php';

        $path = database_path('actions/' . $filename);

        $this->assertFileDoesNotExist($path);

        $this->artisan('make:migration:action')->run();

        $this->assertFileExists($path);
    }
}
