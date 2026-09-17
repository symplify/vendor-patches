<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\FileSystem;

use PHPUnit\Framework\TestCase;
use Symplify\VendorPatches\FileSystem\OldFilesFinder;

final class OldFilesFinderTest extends TestCase
{
    public function test(): void
    {
        $oldFiles = OldFilesFinder::findOldFiles(__DIR__ . '/Fixture');

        $this->assertCount(1, $oldFiles);
        $this->assertStringEndsWith('some/package/File.php.old', $oldFiles[0]);
    }
}
