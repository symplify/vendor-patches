<?php

declare (strict_types=1);
namespace VendorPatches202208\Symplify\SymplifyKernel\Config\Loader;

use VendorPatches202208\Symfony\Component\Config\FileLocator;
use VendorPatches202208\Symfony\Component\Config\Loader\DelegatingLoader;
use VendorPatches202208\Symfony\Component\Config\Loader\GlobFileLoader;
use VendorPatches202208\Symfony\Component\Config\Loader\LoaderResolver;
use VendorPatches202208\Symfony\Component\DependencyInjection\ContainerBuilder;
use VendorPatches202208\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
use VendorPatches202208\Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface;
final class ParameterMergingLoaderFactory implements LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \VendorPatches202208\Symfony\Component\Config\Loader\LoaderInterface
    {
        $fileLocator = new FileLocator([$currentWorkingDirectory]);
        $loaders = [new GlobFileLoader($fileLocator), new ParameterMergingPhpFileLoader($containerBuilder, $fileLocator)];
        $loaderResolver = new LoaderResolver($loaders);
        return new DelegatingLoader($loaderResolver);
    }
}
