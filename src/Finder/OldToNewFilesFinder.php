<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Finder;

use Symplify\VendorPatches\Composer\PackageNameResolver;
use Symplify\VendorPatches\FileSystem\OldFilesFinder;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;

/**
 * @see \Symplify\VendorPatches\Tests\Finder\OldToNewFilesFinderTest
 */
final readonly class OldToNewFilesFinder
{
    public function __construct(
        private PackageNameResolver $packageNameResolver
    ) {
    }

    /**
     * @return OldAndNewFile[]
     */
    public function find(string $directory, bool $resolveFromDirectory = false): array
    {
        $oldAndNewFiles = [];

        $oldFilePaths = OldFilesFinder::findOldFiles($directory);

        foreach ($oldFilePaths as $oldFilePath) {
            // strip the ".old" suffix to get the patched file next to it
            $newFilePath = substr($oldFilePath, 0, -4);
            if (! is_file($newFilePath)) {
                continue;
            }

            if ($resolveFromDirectory) {
                $packageName = $this->packageNameResolver->resolveFromVendorDirectory($newFilePath);
            } else {
                $packageName = $this->packageNameResolver->resolveFromPackageComposerJson($newFilePath);
            }

            $oldAndNewFiles[] = new OldAndNewFile($oldFilePath, $newFilePath, $packageName);
        }

        return $oldAndNewFiles;
    }
}
