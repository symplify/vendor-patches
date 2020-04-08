<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\FileSystem;

use Migrify\VendorPatches\Exception\ShouldNotHappenException;
use Nette\Utils\Strings;

final class PathResolver
{
    /**
     * @var string
     */
    private const VENDOR_PACKAGE_DIRECTORY_PATTERN = '#^(?<vendor_package_directory>.*?vendor\/(\w|\.|\-)+\/(\w|\.|\-)+)\/#is';

    public function resolveVendor(string $absoluteFilePath): string
    {
        $match = Strings::match($absoluteFilePath, self::VENDOR_PACKAGE_DIRECTORY_PATTERN);

        if (! isset($match['vendor_package_directory'])) {
            throw new ShouldNotHappenException('Could not resolve vendor package directory');
        }

        return $match['vendor_package_directory'];
    }
}
