<?php

declare (strict_types=1);
namespace VendorPatches202212\Symplify\SymplifyKernel\Contract\Config;

use VendorPatches202212\Symfony\Component\Config\Loader\LoaderInterface;
use VendorPatches202212\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
