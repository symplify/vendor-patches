<?php

declare (strict_types=1);
namespace Symplify\VendorPatches\FileSystem;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
/**
 * @see \Symplify\VendorPatches\Tests\FileSystem\OldFilesFinderTest
 */
final class OldFilesFinder
{
    /**
     * @var string[]
     */
    private const EXCLUDED_DIRECTORIES = ['composer', 'ocramius'];
    /**
     * @return string[]
     */
    public static function findOldFiles(string $directory) : array
    {
        $recursiveDirectoryIterator = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $recursiveIteratorIterator = new RecursiveIteratorIterator($recursiveDirectoryIterator);
        $oldFilePaths = [];
        foreach ($recursiveIteratorIterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }
            if (\substr_compare($fileInfo->getFilename(), '.old', -\strlen('.old')) !== 0) {
                continue;
            }
            if (self::isInExcludedDirectory($fileInfo->getPathname())) {
                continue;
            }
            $oldFilePaths[] = $fileInfo->getPathname();
        }
        return $oldFilePaths;
    }
    private static function isInExcludedDirectory(string $filePath) : bool
    {
        $found = \false;
        foreach (self::EXCLUDED_DIRECTORIES as $excludedDirectory) {
            if (\strpos($filePath, '/' . $excludedDirectory . '/') !== \false) {
                $found = \true;
                break;
            }
        }
        return $found;
    }
}
