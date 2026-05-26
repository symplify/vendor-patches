<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Composer;

use Entropy\Utils\FileSystem;
use Symplify\VendorPatches\Exception\ShouldNotHappenException;
use Symplify\VendorPatches\FileSystem\PathResolver;
use Webmozart\Assert\Assert;

/**
 * @see \Symplify\VendorPatches\Tests\Composer\PackageNameResolverTest
 */
final class PackageNameResolver
{
    public function resolveFromPackageComposerJson(string $vendorFile): string
    {
        $packageComposerJsonFilePath = $this->getPackageComposerJsonFilePath($vendorFile);

        $composerJson = FileSystem::loadFileToJson($packageComposerJsonFilePath);
        if (! isset($composerJson['name'])) {
            throw new ShouldNotHappenException();
        }

        return $composerJson['name'];
    }

    public function resolveFromVendorDirectory(string $vendorFile): string
    {
        $vendorPackageDirectory = PathResolver::resolveVendorDirectory($vendorFile);

        return basename(dirname($vendorPackageDirectory)) . '/' . basename($vendorPackageDirectory);
    }

    private function getPackageComposerJsonFilePath(string $vendorFilePath): string
    {
        $vendorPackageDirectory = PathResolver::resolveVendorDirectory($vendorFilePath);

        $packageComposerJsonFilePath = $vendorPackageDirectory . '/composer.json';
        Assert::fileExists($packageComposerJsonFilePath);

        return $packageComposerJsonFilePath;
    }
}
