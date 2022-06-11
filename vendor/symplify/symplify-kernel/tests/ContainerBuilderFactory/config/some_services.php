<?php

declare (strict_types=1);
namespace VendorPatches20220611;

use VendorPatches20220611\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VendorPatches20220611\Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};
