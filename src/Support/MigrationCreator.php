<?php

namespace Helldar\LaravelActions\Support;

use Illuminate\Database\Migrations\MigrationCreator as BaseMigrationCreator;
use Illuminate\Filesystem\Filesystem;

class MigrationCreator extends BaseMigrationCreator
{
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

    protected function getStub($table, $create)
    {
        $stub = $this->stubPath() . '/action.stub';

        return $this->files->get($stub);
    }
}
