<?php

declare(strict_types=1);

namespace Symplify\VendorPatches;

use Entropy\Attributes\RelatedTest;
use Entropy\Utils\Strings;
use Symplify\VendorPatches\FileSystem\PathResolver;
use Symplify\VendorPatches\Tests\PatchFileFactory\PatchFileFactoryTest;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;

#[RelatedTest(testClass: PatchFileFactoryTest::class)]
final class PatchFileFactory
{
    private string $outputFolder = 'patches';

    public function createPatchFilePath(OldAndNewFile $oldAndNewFile, string $vendorDirectory): string
    {
        $inVendorRelativeFilePath = PathResolver::getRelativeFilePathFromDirectory(
            $oldAndNewFile->getNewFilePath(),
            $vendorDirectory
        );

        $relativeFilePathWithoutSuffix = strtolower($inVendorRelativeFilePath);
        $pathFileName = Strings::webalize($relativeFilePathWithoutSuffix) . '.patch';

        return $this->outputFolder . '/' . $pathFileName;
    }

    public function setOutputFolder(string $outputDirectory): void
    {
        $this->outputFolder = $outputDirectory;
    }
}
