<?php

declare (strict_types=1);
namespace VendorPatches20220613\Symplify\SymplifyKernel\Contract\Config;

use VendorPatches20220613\Symfony\Component\Config\Loader\LoaderInterface;
use VendorPatches20220613\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
