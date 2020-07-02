<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Tests\Finder;

use Migrify\VendorPatches\Finder\OldToNewFilesFinder;
use Migrify\VendorPatches\HttpKernel\VendorPatchesKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class VendorFilesFinderTest extends AbstractKernelTestCase
{
    /**
     * @var OldToNewFilesFinder
     */
    private $vendorFilesFinder;

    protected function setUp(): void
    {
        self::bootKernel(VendorPatchesKernel::class);

        $this->vendorFilesFinder = self::$container->get(OldToNewFilesFinder::class);
    }

    public function test(): void
    {
        $files = $this->vendorFilesFinder->find(__DIR__ . '/VendorFilesFinderSource');

        $this->assertCount(1, $files);
    }
}
