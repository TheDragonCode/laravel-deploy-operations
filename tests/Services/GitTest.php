<?php

declare(strict_types=1);

namespace Tests\Services;

use DragonCode\LaravelActions\Facades\Git;
use Tests\TestCase;

class GitTest extends TestCase
{
    public function testCurrentBranchNull()
    {
        $this->assertNull(Git::currentBranch(__DIR__));
    }

    public function testCurrentBranch()
    {
        $branch = Git::currentBranch(__DIR__ . '/../../.git');

        $this->assertIsString($branch);
    }
}
