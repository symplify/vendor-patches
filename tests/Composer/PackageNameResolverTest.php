<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Tests\Composer;

use Migrify\VendorPatches\Composer\PackageNameResolver;
use Migrify\VendorPatches\HttpKernel\VendorPatchesKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PackageNameResolverTest extends AbstractKernelTestCase
{
    /**
     * @var PackageNameResolver
     */
    private $packageNameResolver;

    protected function setUp(): void
    {
        self::bootKernel(VendorPatchesKernel::class);

        $this->packageNameResolver = self::$container->get(PackageNameResolver::class);
    }

    public function test(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/PackageNameResolverSource/vendor/some/package/composer.json');

        $packageName = $this->packageNameResolver->resolveFromFileInfo($fileInfo);
        $this->assertSame('some/name', $packageName);
    }
}
