<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use Tests\TestCase;

class MakeTest extends TestCase
{
    public function testMakingFiles()
    {
        $name = 'MakeExample';

        $path = $this->getActionsPath() . '/' . date('Y_m_d_His') . '_make_example.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::MAKE, compact('name'))->assertSuccessful();

        $this->assertFileExists($path);

        $expected = __DIR__ . '/../fixtures/app/stubs/make_example.stub';

        $this->assertEquals(
            file_get_contents($expected),
            file_get_contents($path)
        );
    }

    public function testAutoName()
    {
        $path = $this->getActionsPath() . '/' . date('Y_m_d_His') . '_auto.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::MAKE)->assertSuccessful();

        $this->assertFileExists($path);
    }
}
