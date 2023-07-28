<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\Composer;

use Symplify\VendorPatches\Composer\PackageNameResolver;
use Symplify\VendorPatches\Tests\AbstractTestCase;

final class PackageNameResolverTest extends AbstractTestCase
{
    public function test(): void
    {
        $packageNameResolver = $this->make(PackageNameResolver::class);

        $packageName = $packageNameResolver->resolveFromFilePath(
            __DIR__ . '/PackageNameResolverSource/vendor/some/pac.kage/composer.json'
        );

        $this->assertSame('some/name', $packageName);
    }
}
