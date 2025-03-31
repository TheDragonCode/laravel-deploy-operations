<?php

declare(strict_types=1);

namespace Tests\Commands;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\Support\Facades\Filesystem\File;
use Tests\TestCase;

class MakeTest extends TestCase
{
    public function testMakingFiles()
    {
        $name = 'MakeExample';

        $path = $this->getOperationsPath() . '/' . date('Y_m_d_His') . '_make_example.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::Make, compact('name'))->assertExitCode(0);

        $this->assertFileExists($path);

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../fixtures/app/stubs/make_example.stub'),
            file_get_contents($path)
        );
    }

    public function testAskedName()
    {
        $path = $this->getOperationsPath() . '/' . date('Y_m_d_His') . '_some_name.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::Make)
            ->expectsQuestion('What should the operation be named?', 'Some Name')
            ->assertExitCode(0);

        $this->assertFileExists($path);
    }

    public function testAutoName()
    {
        $path = $this->getOperationsPath() . '/' . date('Y_m_d_His') . '_auto.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::Make)
            ->expectsQuestion('What should the operation be named?', '')
            ->assertExitCode(0);

        $this->assertFileExists($path);
    }

    public function testNestedRightSlashWithoutExtension()
    {
        $name = 'Foo/bar/QweRty';

        $path = $this->getOperationsPath() . '/foo/bar/' . date('Y_m_d_His') . '_qwe_rty.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::Make, compact('name'))->assertExitCode(0);

        $this->assertFileExists($path);

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../fixtures/app/stubs/make_example.stub'),
            file_get_contents($path)
        );
    }

    public function testNestedRightSlashWithExtension()
    {
        $name = 'Foo/bar/QweRty.php';

        $path = $this->getOperationsPath() . '/foo/bar/' . date('Y_m_d_His') . '_qwe_rty.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::Make, compact('name'))->assertExitCode(0);

        $this->assertFileExists($path);

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../fixtures/app/stubs/make_example.stub'),
            file_get_contents($path)
        );
    }

    public function testNestedLeftSlashWithoutExtension()
    {
        $name = 'Foo\\bar\\QweRty';

        $path = $this->getOperationsPath() . '/foo/bar/' . date('Y_m_d_His') . '_qwe_rty.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::Make, compact('name'))->assertExitCode(0);

        $this->assertFileExists($path);

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../fixtures/app/stubs/make_example.stub'),
            file_get_contents($path)
        );
    }

    public function testNestedLeftSlashWithExtension()
    {
        $name = 'Foo\\bar\\QweRty.php';

        $path = $this->getOperationsPath() . '/foo/bar/' . date('Y_m_d_His') . '_qwe_rty.php';

        $this->assertFileDoesNotExist($path);

        $this->artisan(Names::Make, compact('name'))->assertExitCode(0);

        $this->assertFileExists($path);

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../fixtures/app/stubs/make_example.stub'),
            file_get_contents($path)
        );
    }

    public function testFromCustomizedStub()
    {
        $name = 'MakeExample';

        $stubPath = base_path('stubs/deploy-operation.stub');

        $operationPath = $this->getOperationsPath() . '/' . date('Y_m_d_His') . '_make_example.php';

        $this->assertFileDoesNotExist($stubPath);

        File::copy(__DIR__ . '/../fixtures/app/stubs/customized.stub', $stubPath);

        $this->assertFileExists($stubPath);
        $this->assertFileDoesNotExist($operationPath);

        $this->artisan(Names::Make, compact('name'))->assertExitCode(0);

        $this->assertFileExists($operationPath);

        $content = file_get_contents($operationPath);

        $this->assertStringContainsString('Foo\\Bar\\Some', $content);
        $this->assertStringContainsString('extends Some', $content);

        $this->assertStringNotContainsString('DragonCode\\LaravelDeployOperations\\Operation', $content);
        $this->assertStringNotContainsString('extends Operation', $content);
    }
}
