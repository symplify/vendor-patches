<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Tests\FileSystem;

use Migrify\VendorPatches\FileSystem\PathResolver;
use Migrify\VendorPatches\HttpKernel\VendorPatchesKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class PathResolverTest extends AbstractKernelTestCase
{
    /**
     * @var PathResolver
     */
    private $pathResolver;

    protected function setUp(): void
    {
        self::bootKernel(VendorPatchesKernel::class);

        $this->pathResolver = self::$container->get(PathResolver::class);
    }

    public function test(): void
    {
        $absolutePath = '/var/www/project/Vendor/mtdowling/jmespath.php/tests/EnvTest.php';
        $vendor = $this->pathResolver->resolveVendor($absolutePath);

        $this->assertSame('/var/www/project/Vendor/mtdowling/jmespath.php', $vendor);
    }
}
