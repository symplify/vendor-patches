<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\VendorPatches\Composer\PackageNameResolver;
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
    public function find(string $directory): array
    {
        $oldAndNewFiles = [];

        $oldFilePaths = $this->findFilePathsInDirectory($directory);

        foreach ($oldFilePaths as $oldFilePath) {
            $oldStrrPos = (int) strrpos($oldFilePath, '.old');
            if (strlen($oldFilePath) - $oldStrrPos !== 4) {
                continue;
            }

            $newFilePath = substr($oldFilePath, 0, $oldStrrPos);
            if (! file_exists($newFilePath)) {
                continue;
            }

            $packageName = $this->packageNameResolver->resolveFromFilePath($newFilePath);
            $oldAndNewFiles[] = new OldAndNewFile($oldFilePath, $newFilePath, $packageName);
        }

        return $oldAndNewFiles;
    }

    /**
     * @return string[]
     */
    private function findFilePathsInDirectory(string $directory): array
    {
        $finder = Finder::create()
            ->in($directory)
            ->files()
            // excluded built files
            ->exclude('composer/')
            ->exclude('ocramius/')
            ->name('*.old');

        $fileInfos = iterator_to_array($finder->getIterator());
        return array_keys($fileInfos);
    }
}
