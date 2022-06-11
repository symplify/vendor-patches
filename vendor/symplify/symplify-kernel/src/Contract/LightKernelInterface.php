<?php

declare (strict_types=1);
namespace VendorPatches20220611\Symplify\SymplifyKernel\Contract;

use VendorPatches20220611\Psr\Container\ContainerInterface;
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
