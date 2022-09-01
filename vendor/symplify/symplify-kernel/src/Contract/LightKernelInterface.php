<?php

declare (strict_types=1);
namespace VendorPatches202209\Symplify\SymplifyKernel\Contract;

use VendorPatches202209\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : ContainerInterface;
    public function getContainer() : ContainerInterface;
}
