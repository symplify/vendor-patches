<?php

declare(strict_types=1);

namespace Symplify\VendorPatches;

use Nette\Utils\Strings;
use Symplify\VendorPatches\FileSystem\PathResolver;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;

/**
 * @see \Symplify\VendorPatches\Tests\PatchFileFactory\PatchFileFactoryTest
 */
final class PatchFileFactory
{
    public const DEFAULT_OUTPUT_PATH = 'patches';

    public const OUTPUT_PATH_ENV_VAR = 'VENDOR_PATCHES_OUTPUT_PATH';

    public function createPatchFilePath(OldAndNewFile $oldAndNewFile, string $vendorDirectory): string
    {
        $inVendorRelativeFilePath = PathResolver::getRelativeFilePathFromDirectory(
            $oldAndNewFile->getNewFilePath(),
            $vendorDirectory
        );

        $relativeFilePathWithoutSuffix = Strings::lower($inVendorRelativeFilePath);
        $patchFileName = Strings::webalize($relativeFilePathWithoutSuffix) . '.patch';

        return $this->getOutputPathRelativeToProjectRoot() . DIRECTORY_SEPARATOR . $patchFileName;
    }

    private function getOutputPathRelativeToProjectRoot(): string
    {
        $outputPath = getenv(self::OUTPUT_PATH_ENV_VAR);

        if ($outputPath) {
            if (!str_starts_with($outputPath, PathResolver::getAbsoluteRootPath())) {
                return $outputPath;
            }

            $projectRootPath = PathResolver::getProjectRootPath();
            if (str_starts_with($outputPath, $projectRootPath)) {
                return PathResolver::getRelativeFilePathFromDirectory($outputPath, $projectRootPath);
            }
        }

        return self::DEFAULT_OUTPUT_PATH;
    }
}
