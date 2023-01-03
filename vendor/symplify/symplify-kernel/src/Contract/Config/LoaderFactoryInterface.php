<?php

declare (strict_types=1);
namespace VendorPatches202301\Symplify\SymplifyKernel\Contract\Config;

use VendorPatches202301\Symfony\Component\Config\Loader\LoaderInterface;
use VendorPatches202301\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
