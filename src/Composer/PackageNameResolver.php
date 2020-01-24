<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Composer;

use Migrify\VendorPatches\Exception\ShouldNotHappenException;
use Migrify\VendorPatches\Json\JsonFileSystem;
use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PackageNameResolver
{
    /**
     * @var string
     */
    private const VENDOR_PACKAGE_DIRECTORY_PATTERN = '#^(?<vendor_package_directory>.*?vendor\/(\w|\-)+\/(\w|-)+)\/#is';

    /**
     * @var JsonFileSystem
     */
    private $jsonFileSystem;

    public function __construct(JsonFileSystem $jsonFileSystem)
    {
        $this->jsonFileSystem = $jsonFileSystem;
    }

    public function resolveFromFileInfo(SmartFileInfo $vendorFile): string
    {
        $vendorPackageDirectory = $this->resolveVendorPackageDirectory($vendorFile);

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

    private function resolveVendorPackageDirectory(SmartFileInfo $vendorFile): string
    {
        $match = Strings::match($vendorFile->getRealPath(), self::VENDOR_PACKAGE_DIRECTORY_PATTERN);
        if (! isset($match['vendor_package_directory'])) {
            throw new ShouldNotHappenException('Could not resolve vendor package directory');
        }

        return $match['vendor_package_directory'];
    }
}
