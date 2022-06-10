<?php

declare (strict_types=1);
namespace VendorPatches20220610\Symplify\SymplifyKernel\Contract;

use VendorPatches20220610\Psr\Container\ContainerInterface;
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
