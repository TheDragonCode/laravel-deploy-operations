<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\Support\Facades\Filesystem\File;
use Tests\TestCase;

class MakeTest extends TestCase
{
    public function testMakingFiles()
    {
        $name = 'MakeExample';

        $path = $this->getActionsPath() . '/' . date('Y_m_d_His') . '_make_example.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::MAKE, compact('name'))->assertExitCode(0);

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

        $this->artisan(Names::MAKE)->assertExitCode(0);

        $this->assertFileExists($path);
    }

    public function testNestedRightSlashWithoutExtension()
    {
        $name = 'Foo/bar/QweRty';

        $path = $this->getActionsPath() . '/foo/bar/' . date('Y_m_d_His') . '_qwe_rty.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::MAKE, compact('name'))->assertExitCode(0);

        $this->assertFileExists($path);

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../fixtures/app/stubs/make_example.stub'),
            file_get_contents($path)
        );
    }

    public function testNestedRightSlashWithExtension()
    {
        $name = 'Foo/bar/QweRty.php';

        $path = $this->getActionsPath() . '/foo/bar/' . date('Y_m_d_His') . '_qwe_rty.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::MAKE, compact('name'))->assertExitCode(0);

        $this->assertFileExists($path);

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../fixtures/app/stubs/make_example.stub'),
            file_get_contents($path)
        );
    }

    public function testNestedLeftSlashWithoutExtension()
    {
        $name = 'Foo\\bar\\QweRty';

        $path = $this->getActionsPath() . '/foo/bar/' . date('Y_m_d_His') . '_qwe_rty.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::MAKE, compact('name'))->assertExitCode(0);

        $this->assertFileExists($path);

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../fixtures/app/stubs/make_example.stub'),
            file_get_contents($path)
        );
    }

    public function testNestedLeftSlashWithExtension()
    {
        $name = 'Foo\\bar\\QweRty.php';

        $path = $this->getActionsPath() . '/foo/bar/' . date('Y_m_d_His') . '_qwe_rty.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::MAKE, compact('name'))->assertExitCode(0);

        $this->assertFileExists($path);

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../fixtures/app/stubs/make_example.stub'),
            file_get_contents($path)
        );
    }

    public function testFromCustomizedStub()
    {
        $name = 'MakeExample';

        $stubPath = base_path('stubs/action.stub');

        $actionPath = $this->getActionsPath() . '/' . date('Y_m_d_His') . '_make_example.php';

        $this->assertFileDoesNotExist($stubPath);

        File::copy(__DIR__ . '/../fixtures/app/stubs/customized.stub', $stubPath);

        $this->assertFileExists($stubPath);
        $this->assertFileDoesNotExist($actionPath);

        $this->artisan(Names::MAKE, compact('name'))->assertExitCode(0);

        $this->assertFileExists($actionPath);

        $content = file_get_contents($actionPath);

        $this->assertStringContainsString('Foo\\Bar\\Some', $content);
        $this->assertStringContainsString('extends Some', $content);

        $this->assertStringNotContainsString('DragonCode\\LaravelActions\\Action', $content);
        $this->assertStringNotContainsString('extends Action', $content);
        $this->assertStringNotContainsString('Run the actions.', $content);
        $this->assertStringNotContainsString('@return void', $content);
    }
}
