<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Finder;

use Migrify\VendorPatches\Validation\FileSystemValidator;
use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class VendorFilesFinder
{
    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    /**
     * @var FileSystemValidator
     */
    private $fileSystemValidator;

    public function __construct(FinderSanitizer $finderSanitizer, FileSystemValidator $fileSystemValidator)
    {
        $this->finderSanitizer = $finderSanitizer;
        $this->fileSystemValidator = $fileSystemValidator;
    }

    /**
     * @return SmartFileInfo[]
     */
    public function find(string $directory): array
    {
        $this->fileSystemValidator->ensureIsDirectoryAndDirectoryExists($directory);

        $smartFileInfos = $this->findSmartFileInfosInDirectory($directory);

        // change key to relative file path to vendor
        return $this->groupSmartFileInfosByRelativeDirectoryFilePath($smartFileInfos, $directory);
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findSmartFileInfosInDirectory(string $directory): array
    {
        $finder = Finder::create()
            ->in($directory)
            ->files()
            // excluded builded files
            ->exclude('composer/')
            ->exclude('ocramius/')
            ->name('*.php');

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @param SmartFileInfo[] $smartFileInfos
     * @return SmartFileInfo[]
     */
    private function groupSmartFileInfosByRelativeDirectoryFilePath(array $smartFileInfos, string $directory): array
    {
        $smartFileInfosByRelativeDirectoryFilePath = [];
        foreach ($smartFileInfos as $smartFileInfo) {
            $relativeDirectoryFilePath = $smartFileInfo->getRelativeFilePathFromDirectory($directory);

            $smartFileInfosByRelativeDirectoryFilePath[$relativeDirectoryFilePath] = $smartFileInfo;
        }

        return $smartFileInfosByRelativeDirectoryFilePath;
    }
}
