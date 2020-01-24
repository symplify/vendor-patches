<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Composer;

use Migrify\VendorPatches\Exception\ShouldNotHappenException;
use Migrify\VendorPatches\FileSystem\PathResolver;
use Migrify\VendorPatches\Json\JsonFileSystem;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PackageNameResolver
{
    /**
     * @var JsonFileSystem
     */
    private $jsonFileSystem;

    /**
     * @var PathResolver
     */
    private $pathResolver;

    public function __construct(JsonFileSystem $jsonFileSystem, PathResolver $pathResolver)
    {
        $this->jsonFileSystem = $jsonFileSystem;
        $this->pathResolver = $pathResolver;
    }

    public function resolveFromFileInfo(SmartFileInfo $vendorFile): string
    {
        $vendorPackageDirectory = $this->pathResolver->resolveVendor($vendorFile->getRealPath());

        $packageComposerJsonFilePath = $vendorPackageDirectory . '/composer.json';
        if (! file_exists($packageComposerJsonFilePath)) {
            throw new ShouldNotHappenException();
        }

        $composerJson = $this->jsonFileSystem->loadFilePathToJson($packageComposerJsonFilePath);
        if (! isset($composerJson['name'])) {
            throw new ShouldNotHappenException();
        }

        return $composerJson['name'];
    }
}
