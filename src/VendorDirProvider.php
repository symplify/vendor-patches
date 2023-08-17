<?php

declare(strict_types=1);

namespace Symplify\VendorPatches;

use Composer\Autoload\ClassLoader;
use ReflectionClass;
use Symplify\VendorPatches\FileSystem\PathResolver;
use Webmozart\Assert\Assert;

final class VendorDirProvider
{
    public static function provide(): string
    {
        $absoluteRootPath = PathResolver::getAbsoluteRootPath();

        $path = __DIR__;
        while (! \str_ends_with($path, 'vendor') && $path !== $absoluteRootPath) {
            $path = dirname($path);
        }

        if ($path !== $absoluteRootPath) {
            return $path;
        }

        return self::reflectionFallback();
    }

    private static function reflectionFallback(): string
    {
        $reflectionClass = new ReflectionClass(ClassLoader::class);

        $classLoaderFileName = $reflectionClass->getFileName();
        Assert::string($classLoaderFileName);

        return dirname($classLoaderFileName, 2);
    }
}
