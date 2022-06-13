<?php

declare (strict_types=1);
namespace VendorPatches202206\Symplify\SymplifyKernel\Contract\Config;

use VendorPatches202206\Symfony\Component\Config\Loader\LoaderInterface;
use VendorPatches202206\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
