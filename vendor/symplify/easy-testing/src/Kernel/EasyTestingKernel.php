<?php

declare (strict_types=1);
namespace VendorPatches20220610\Symplify\EasyTesting\Kernel;

use VendorPatches20220610\Psr\Container\ContainerInterface;
use VendorPatches20220610\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use VendorPatches20220610\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : ContainerInterface
    {
        $configFiles[] = EasyTestingConfig::FILE_PATH;
        return $this->create($configFiles);
    }
}