<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Composer;

use Migrify\VendorPatches\Exception\ShouldNotHappenException;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PackageNameResolver
{
    public function resolveFromFileInfo(SmartFileInfo $vendorFile): string
    {
        $vendorPackageDirectory = $this->resolveVendorPackageDirectory($vendorFile);

        $packageComposerJsonFilePath = $vendorPackageDirectory . '/composer.json';
        if (! file_exists($packageComposerJsonFilePath)) {
            throw new ShouldNotHappenException();
        }

        $composerJson = $this->loadFileToJson($packageComposerJsonFilePath);
        if (! isset($composerJson['name'])) {
            throw new ShouldNotHappenException();
        }

        return $composerJson['name'];
    }

    private function resolveVendorPackageDirectory(SmartFileInfo $vendorFile): string
    {
        $match = Strings::match($vendorFile->getRealPath(), '#^(?<vendor_package_directory>.*?vendor\/\w+\/\w+)\/#');
        if (! isset($match['vendor_package_directory'])) {
            throw new ShouldNotHappenException();
        }

        return $match['vendor_package_directory'];
    }

    private function loadFileToJson(string $filePath): array
    {
        $composerJsonFileContent = FileSystem::read($filePath);

        return Json::decode($composerJsonFileContent, Json::FORCE_ARRAY);
    }
}
