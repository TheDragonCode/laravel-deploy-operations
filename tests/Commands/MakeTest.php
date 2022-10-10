<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Console\Command;
use DragonCode\LaravelActions\Constants\Names;
use Tests\TestCase;

class MakeTest extends TestCase
{
    public function testMakingFiles()
    {
        $name = 'MakeExample';

        $path = $this->getActionsPath() . '/' . date('Y_m_d_His') . '_make_example.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::MAKE, compact('name'))->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($path);

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../fixtures/app/stubs/make_example.stub'),
            file_get_contents($path)
        );
    }

    public function testAutoName()
    {
        $path = $this->getActionsPath() . '/' . date('Y_m_d_His') . '_auto.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::MAKE)->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($path);
    }
}
