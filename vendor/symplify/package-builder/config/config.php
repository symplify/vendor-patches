<?php

declare (strict_types=1);
namespace VendorPatches202210;

use VendorPatches202210\SebastianBergmann\Diff\Differ;
use VendorPatches202210\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VendorPatches202210\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use VendorPatches202210\Symplify\PackageBuilder\Console\Output\ConsoleDiffer;
use VendorPatches202210\Symplify\PackageBuilder\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use VendorPatches202210\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->set(ColorConsoleDiffFormatter::class);
    $services->set(ConsoleDiffer::class);
    $services->set(CompleteUnifiedDiffOutputBuilderFactory::class);
    $services->set(Differ::class);
    $services->set(PrivatesAccessor::class);
};
