<?php

declare(strict_types=1);

namespace Symplify\VendorPatches;

use Composer\Autoload\ClassLoader;
use ReflectionClass;
use Webmozart\Assert\Assert;

final class VendorDirProvider
{
    public static function provide(): string
    {
        $rootFolder = getenv('SystemDrive', true) . DIRECTORY_SEPARATOR;

        $path = __DIR__;
        while (! \str_ends_with($path, 'vendor') && $path !== $rootFolder) {
            $path = dirname($path);
        }

        if ($path !== $rootFolder) {
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
