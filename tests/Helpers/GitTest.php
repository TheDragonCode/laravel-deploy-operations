<?php

declare(strict_types=1);

namespace Tests\Helpers;

use DragonCode\LaravelDeployOperations\Helpers\GitHelper;
use PHPUnit\Framework\TestCase;

class GitTest extends TestCase
{
    public function testCurrentBranchNull(): void
    {
        $this->assertNull($this->git()->currentBranch(__DIR__));
    }

    public function testCurrentBranch(): void
    {
        $branch = $this->git()->currentBranch(__DIR__ . '/../../');

        $this->assertIsString($branch);
    }

    protected function git(): GitHelper
    {
        return new GitHelper;
    }
}
