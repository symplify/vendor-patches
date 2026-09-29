<?php

declare(strict_types=1);

namespace Symplify\VendorPatches;

use Composer\Autoload\ClassLoader;
use ReflectionClass;
use Symplify\VendorPatches\Exception\ShouldNotHappenException;

final class VendorDirProvider
{
    public static function provideProjectVendorDirectory(): string
    {
        $cwdVendorDirectory = getcwd() . '/vendor';
        if (is_dir($cwdVendorDirectory)) {
            return $cwdVendorDirectory;
        }

        return self::provide();
    }

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
        if (! is_string($classLoaderFileName)) {
            throw new ShouldNotHappenException('Composer ClassLoader file was not found');
        }

        return dirname($classLoaderFileName, 2);
    }
}
