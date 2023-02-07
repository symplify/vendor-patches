<?php

declare (strict_types=1);
namespace VendorPatches202302;

use VendorPatches202302\SebastianBergmann\Diff\Differ;
use VendorPatches202302\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VendorPatches202302\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use VendorPatches202302\Symplify\PackageBuilder\Console\Output\ConsoleDiffer;
use VendorPatches202302\Symplify\PackageBuilder\Diff\DifferFactory;
use VendorPatches202302\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use function VendorPatches202302\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->set(ColorConsoleDiffFormatter::class);
    $services->set(ConsoleDiffer::class);
    $services->set(DifferFactory::class);
    $services->set(Differ::class)->factory([service(DifferFactory::class), 'create']);
    $services->set(PrivatesAccessor::class);
};
