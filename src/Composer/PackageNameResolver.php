<?php

declare (strict_types=1);
namespace Symplify\VendorPatches\Composer;

use VendorPatches202502\Nette\Utils\FileSystem;
use VendorPatches202502\Nette\Utils\Json;
use Symplify\VendorPatches\Exception\ShouldNotHappenException;
use Symplify\VendorPatches\FileSystem\PathResolver;
use VendorPatches202502\Webmozart\Assert\Assert;
/**
 * @see \Symplify\VendorPatches\Tests\Composer\PackageNameResolverTest
 */
final class PackageNameResolver
{
    public function resolveFromFilePath(string $vendorFile) : string
    {
        $packageComposerJsonFilePath = $this->getPackageComposerJsonFilePath($vendorFile);
        $packageComposerContents = FileSystem::read($packageComposerJsonFilePath);
        $composerJson = Json::decode($packageComposerContents, Json::FORCE_ARRAY);
        if (!isset($composerJson['name'])) {
            throw new ShouldNotHappenException();
        }
        return $composerJson['name'];
    }
    private function getPackageComposerJsonFilePath(string $vendorFilePath) : string
    {
        $vendorPackageDirectory = PathResolver::resolveVendorDirectory($vendorFilePath);
        $packageComposerJsonFilePath = $vendorPackageDirectory . '/composer.json';
        Assert::fileExists($packageComposerJsonFilePath);
        return $packageComposerJsonFilePath;
    }
}
