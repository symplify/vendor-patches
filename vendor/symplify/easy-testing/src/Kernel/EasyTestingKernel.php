<?php

declare (strict_types=1);
namespace VendorPatches202302\Symplify\EasyTesting\Kernel;

use VendorPatches202302\Psr\Container\ContainerInterface;
use VendorPatches202302\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use VendorPatches202302\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
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
