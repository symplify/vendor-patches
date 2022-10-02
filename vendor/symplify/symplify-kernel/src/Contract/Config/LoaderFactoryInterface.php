<?php

declare (strict_types=1);
namespace VendorPatches202210\Symplify\SymplifyKernel\Contract\Config;

use VendorPatches202210\Symfony\Component\Config\Loader\LoaderInterface;
use VendorPatches202210\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
