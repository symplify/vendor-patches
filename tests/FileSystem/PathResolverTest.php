<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Tests\FileSystem;

use Migrify\VendorPatches\FileSystem\PathResolver;
use Migrify\VendorPatches\HttpKernel\VendorPatchesKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class PathResolverTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private const ABSOLUTE_PATH = '/var/www/project/Vendor/mtdowling/jmespath.php/tests/EnvTest.php';

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
        $vendor = $this->pathResolver->resolveVendor(self::ABSOLUTE_PATH);
        $this->assertSame('/var/www/project/Vendor/mtdowling/jmespath.php', $vendor);
    }
}
