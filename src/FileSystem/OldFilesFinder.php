<?php

declare(strict_types=1);

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
    private const array EXCLUDED_DIRECTORIES = ['composer', 'ocramius'];

    /**
     * @return string[]
     */
    public static function findOldFiles(string $directory): array
    {
        $recursiveDirectoryIterator = new RecursiveDirectoryIterator(
            $directory,
            RecursiveDirectoryIterator::SKIP_DOTS
        );

        $recursiveIteratorIterator = new RecursiveIteratorIterator($recursiveDirectoryIterator);

        $oldFilePaths = [];

        foreach ($recursiveIteratorIterator as $fileInfo) {
            if (! $fileInfo->isFile()) {
                continue;
            }

            if (! str_ends_with($fileInfo->getFilename(), '.old')) {
                continue;
            }

            if (self::isInExcludedDirectory($fileInfo->getPathname())) {
                continue;
            }

            $oldFilePaths[] = $fileInfo->getPathname();
        }

        return $oldFilePaths;
    }

    private static function isInExcludedDirectory(string $filePath): bool
    {
        return array_any(
            self::EXCLUDED_DIRECTORIES,
            fn (string $excludedDirectory): bool => str_contains($filePath, '/' . $excludedDirectory . '/')
        );
    }
}
