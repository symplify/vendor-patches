<?php

declare (strict_types=1);
namespace VendorPatches202207;

use VendorPatches202207\SebastianBergmann\Diff\Differ;
use VendorPatches202207\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VendorPatches202207\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use VendorPatches202207\Symplify\PackageBuilder\Console\Output\ConsoleDiffer;
use VendorPatches202207\Symplify\PackageBuilder\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use VendorPatches202207\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->set(ColorConsoleDiffFormatter::class);
    $services->set(ConsoleDiffer::class);
    $services->set(CompleteUnifiedDiffOutputBuilderFactory::class);
    $services->set(Differ::class);
    $services->set(PrivatesAccessor::class);
};
