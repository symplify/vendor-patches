<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\Composer;

use Symplify\VendorPatches\Composer\PackageNameResolver;
use Symplify\VendorPatches\Tests\AbstractTestCase;

final class PackageNameResolverTest extends AbstractTestCase
{
    public function testResolveFromPackageComposerJson(): void
    {
        $packageNameResolver = $this->make(PackageNameResolver::class);

        $packageName = $packageNameResolver->resolveFromPackageComposerJson(
            __DIR__ . '/PackageNameResolverSource/vendor/some/pac.kage/composer.json'
        );

        $this->assertSame('some/name', $packageName);
    }

    public function testResolveFromVendorDirectory(): void
    {
        $packageNameResolver = $this->make(PackageNameResolver::class);

        $packageName = $packageNameResolver->resolveFromVendorDirectory(
            __DIR__ . '/PackageNameResolverSource/vendor/some/pac.kage/src/SomeFile.php'
        );

        $this->assertSame('some/pac.kage', $packageName);
    }
}
