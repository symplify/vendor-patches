<?php

declare (strict_types=1);
namespace VendorPatches202211\Symplify\SymplifyKernel\Contract\Config;

use VendorPatches202211\Symfony\Component\Config\Loader\LoaderInterface;
use VendorPatches202211\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
