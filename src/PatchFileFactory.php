<?php

declare (strict_types=1);
namespace Symplify\VendorPatches;

use VendorPatches202507\Nette\Utils\Strings;
use Symplify\VendorPatches\FileSystem\PathResolver;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;
/**
 * @see \Symplify\VendorPatches\Tests\PatchFileFactory\PatchFileFactoryTest
 */
final class PatchFileFactory
{
    /**
     * @var string
     */
    private $outputFolder = 'patches';
    public function createPatchFilePath(OldAndNewFile $oldAndNewFile, string $vendorDirectory) : string
    {
        $inVendorRelativeFilePath = PathResolver::getRelativeFilePathFromDirectory($oldAndNewFile->getNewFilePath(), $vendorDirectory);
        $relativeFilePathWithoutSuffix = Strings::lower($inVendorRelativeFilePath);
        $pathFileName = Strings::webalize($relativeFilePathWithoutSuffix) . '.patch';
        return $this->outputFolder . '/' . $pathFileName;
    }
    public function setOutputFolder(string $outputDirectory) : void
    {
        $this->outputFolder = $outputDirectory;
    }
}
