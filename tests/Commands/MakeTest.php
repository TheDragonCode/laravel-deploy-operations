<?php

namespace Tests\Commands;

use DragonCode\LaravelSupport\Facades\AppVersion;
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

        $expected = AppVersion::is9x()
            ? __DIR__ . '/../fixtures/app/9.x/stubs/make_example.stub'
            : __DIR__ . '/../fixtures/app/prev/stubs/make_example.stub';

        $this->assertEquals(file_get_contents($expected), file_get_contents($path));
    }
}
