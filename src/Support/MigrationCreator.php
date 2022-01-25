<?php

namespace DragonCode\LaravelActions\Support;

use DragonCode\LaravelActions\Concerns\Anonymous;
use Illuminate\Database\Migrations\MigrationCreator as BaseMigrationCreator;
use Illuminate\Filesystem\Filesystem;

class MigrationCreator extends BaseMigrationCreator
{
    use Anonymous;

    protected $customStubPath;

    public function __construct(Filesystem $files, ?string $custom_stub_path)
    {
        parent::__construct($files, $custom_stub_path);

        $this->customStubPath = $custom_stub_path;
    }

    public function create($name, $path, $table = null, $create = false)
    {
        $this->files->ensureDirectoryExists($path);

        return parent::create($name, $path, $table, $create);
    }

    public function stubPath()
    {
        return realpath($this->customStubPath);
    }

    protected function getStub($table, $create): string
    {
        $stub = $this->allowAnonymous() ? '/action-anonymous.stub' : '/action-named.stub';

        return $this->files->get(
            $this->stubPath() . $stub
        );
    }
}
