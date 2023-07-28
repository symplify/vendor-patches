<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\VendorPatches\Composer\PackageNameResolver;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;

final class OldToNewFilesFinder
{
    public function __construct(
        private readonly PackageNameResolver $packageNameResolver
    ) {
    }

    /**
     * @return OldAndNewFile[]
     */
    public function find(string $directory): array
    {
        $oldAndNewFileInfos = [];

        $oldFilePaths = $this->findSmartFileInfosInDirectory($directory);

        foreach ($oldFilePaths as $oldFilePath) {
            $oldStrrPos = (int) strrpos((string) $oldFilePath, '.old');
            if (strlen((string) $oldFilePath) - $oldStrrPos !== 4) {
                continue;
            }

            $newFilePath = substr((string) $oldFilePath, 0, $oldStrrPos);
            if (! file_exists($newFilePath)) {
                continue;
            }

            //$newFileInfo = new SmartFileInfo($newFilePath);
            $packageName = $this->packageNameResolver->resolveFromFilePath($newFilePath);

            $oldAndNewFileInfos[] = new OldAndNewFile($oldFilePath, $newFilePath, $packageName);
        }

        return $oldAndNewFileInfos;
    }

    /**
     * @return string[]
     */
    private function findSmartFileInfosInDirectory(string $directory): array
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
