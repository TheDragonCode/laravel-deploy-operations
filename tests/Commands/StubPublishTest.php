<?php

declare(strict_types=1);

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\Support\Facades\Filesystem\File;
use Tests\TestCase;

class StubPublishTest extends TestCase
{
    public function testFirst(): void
    {
        $this->assertFileDoesNotExist($this->path());

        $this->artisan(Names::STUB_PUBLISH)->assertExitCode(0);

        $this->assertFileExists($this->path());
    }

    public function testSkip(): void
    {
        File::copy(__DIR__ . '/../fixtures/app/stubs/customized.stub', $this->path());

        $this->assertFileIsReadable($this->path());
        $this->assertFileExists($this->path());

        $content = file_get_contents($this->path());

        $this->assertStringContainsString('Foo\\Bar\\Some', $content);
        $this->assertStringContainsString('extends Some', $content);

        $this->assertStringNotContainsString('DragonCode\\LaravelActions\\Action', $content);
        $this->assertStringNotContainsString('extends Action', $content);
        $this->assertStringNotContainsString('Run the actions.', $content);
        $this->assertStringNotContainsString('@return void', $content);

        $this->artisan(Names::STUB_PUBLISH)->assertExitCode(0);

        $content = file_get_contents($this->path());

        $this->assertStringContainsString('Foo\\Bar\\Some', $content);
        $this->assertStringContainsString('extends Some', $content);

        $this->assertStringNotContainsString('DragonCode\\LaravelActions\\Action', $content);
        $this->assertStringNotContainsString('extends Action', $content);
        $this->assertStringNotContainsString('Run the actions.', $content);
        $this->assertStringNotContainsString('@return void', $content);
    }

    public function testForce(): void
    {
        File::copy(__DIR__ . '/../fixtures/app/stubs/customized.stub', $this->path());

        $this->assertFileIsReadable($this->path());
        $this->assertFileExists($this->path());

        $content = file_get_contents($this->path());

        $this->assertStringContainsString('Foo\\Bar\\Some', $content);
        $this->assertStringContainsString('extends Some', $content);

        $this->assertStringNotContainsString('DragonCode\\LaravelActions\\Action', $content);
        $this->assertStringNotContainsString('extends Action', $content);
        $this->assertStringNotContainsString('Run the actions.', $content);
        $this->assertStringNotContainsString('@return void', $content);

        $this->artisan(Names::STUB_PUBLISH, [
            '--force' => true,
        ])->assertExitCode(0);

        $content = file_get_contents($this->path());

        $this->assertStringNotContainsString('Foo\\Bar\\Some', $content);
        $this->assertStringNotContainsString('extends Some', $content);

        $this->assertStringContainsString('DragonCode\\LaravelActions\\Action', $content);
        $this->assertStringContainsString('extends Action', $content);
        $this->assertStringContainsString('Run the actions.', $content);
        $this->assertStringContainsString('@return void', $content);
    }

    protected function path(): string
    {
        return base_path('stubs/action.stub');
    }
}
