<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\Finder;

use Symplify\VendorPatches\Finder\OldToNewFilesFinder;
use Symplify\VendorPatches\Tests\AbstractTestCase;

final class VendorFilesFinderTest extends AbstractTestCase
{
    public function test(): void
    {
        $oldToNewFilesFinder = $this->make(OldToNewFilesFinder::class);

        $files = $oldToNewFilesFinder->find(__DIR__ . '/VendorFilesFinderSource');
        $this->assertCount(2, $files);
    }
}
