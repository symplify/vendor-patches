<?php

declare (strict_types=1);
namespace VendorPatches202207;

use VendorPatches202207\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VendorPatches202207\Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};
