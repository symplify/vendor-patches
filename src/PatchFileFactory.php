<?php

declare (strict_types=1);
namespace Symplify\VendorPatches;

use VendorPatches202502\Nette\Utils\Strings;
use Symplify\VendorPatches\FileSystem\PathResolver;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;
/**
 * @see \Symplify\VendorPatches\Tests\PatchFileFactory\PatchFileFactoryTest
 */
final class PatchFileFactory
{
    public function createPatchFilePath(OldAndNewFile $oldAndNewFile, string $vendorDirectory) : string
    {
        $inVendorRelativeFilePath = PathResolver::getRelativeFilePathFromDirectory($oldAndNewFile->getNewFilePath(), $vendorDirectory);
        $relativeFilePathWithoutSuffix = Strings::lower($inVendorRelativeFilePath);
        $pathFileName = Strings::webalize($relativeFilePathWithoutSuffix) . '.patch';
        return 'patches/' . $pathFileName;
    }
}
