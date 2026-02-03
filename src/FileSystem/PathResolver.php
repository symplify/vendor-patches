<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\FileSystem;

use Entropy\Utils\Regex;
use Symplify\VendorPatches\Exception\ShouldNotHappenException;
use Symplify\VendorPatches\Utils\FileSystemHelper;
use Webmozart\Assert\Assert;

final class PathResolver
{
    /**
     * @see https://regex101.com/r/ABxOlD/1
     * @var string
     */
    private const VENDOR_PACKAGE_DIRECTORY_REGEX = '#^(?<vendor_package_directory>.*?vendor/[^/]+/[^/]+)#';

    public static function resolveVendorDirectory(string $filePath): string
    {
        $match = Regex::match(FileSystemHelper::normalizePath($filePath), self::VENDOR_PACKAGE_DIRECTORY_REGEX);
        if (! isset($match['vendor_package_directory'])) {
            throw new ShouldNotHappenException('Could not resolve vendor package directory');
        }

        return $match['vendor_package_directory'];
    }

    public static function getRelativeFilePathFromDirectory(string $filePath, string $directory): string
    {
        Assert::directory($directory);

        // get relative path from directory
        return Regex::replace($filePath, '#^' . preg_quote($directory, '#') . '#', '');
    }
}
