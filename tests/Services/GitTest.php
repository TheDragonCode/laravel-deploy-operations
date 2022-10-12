<?php

declare(strict_types=1);

namespace Tests\Services;

use DragonCode\LaravelActions\Helpers\Git;
use Tests\TestCase;

class GitTest extends TestCase
{
    public function testCurrentBranchNull()
    {
        $this->assertNull($this->git()->currentBranch(__DIR__));
    }

    public function testCurrentBranch()
    {
        $branch = $this->git()->currentBranch(__DIR__ . '/../../');

        $this->assertIsString($branch);
    }

    protected function git(): Git
    {
        return $this->app->make(Git::class);
    }
}
