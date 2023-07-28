<?php

declare(strict_types=1);

namespace Symplify\VendorPatches;

use Nette\Utils\Strings;
use Symplify\VendorPatches\FileSystem\PathResolver;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;

final class PatchFileFactory
{
    public function createPatchFilePath(OldAndNewFile $oldAndNewFileInfo, string $vendorDirectory): string
    {
        $inVendorRelativeFilePath = PathResolver::getRelativeFilePathFromDirectory(
            $vendorDirectory,
            $oldAndNewFileInfo->getNewFilePath()
        );

        $relativeFilePathWithoutSuffix = Strings::lower($inVendorRelativeFilePath);
        $pathFileName = Strings::webalize($relativeFilePathWithoutSuffix) . '.patch';

        return 'patches' . DIRECTORY_SEPARATOR . $pathFileName;
    }
}
