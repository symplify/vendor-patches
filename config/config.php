<?php

declare (strict_types=1);
namespace VendorPatches202301;

use VendorPatches202301\SebastianBergmann\Diff\Differ;
use VendorPatches202301\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use VendorPatches202301\Symfony\Component\Console\Application;
use VendorPatches202301\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VendorPatches202301\Symplify\PackageBuilder\Composer\VendorDirProvider;
use VendorPatches202301\Symplify\PackageBuilder\Yaml\ParametersMerger;
use VendorPatches202301\Symplify\SmartFileSystem\Json\JsonFileSystem;
use Symplify\VendorPatches\Console\VendorPatchesApplication;
use function VendorPatches202301\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('Symplify\\VendorPatches\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);
    $services->set(UnifiedDiffOutputBuilder::class)->args(['$addLineNumbers' => \true]);
    $services->set(Differ::class)->args(['$outputBuilder' => service(UnifiedDiffOutputBuilder::class)]);
    $services->set(VendorDirProvider::class);
    $services->set(JsonFileSystem::class);
    // for autowired commands
    $services->alias(Application::class, VendorPatchesApplication::class);
    $services->set(ParametersMerger::class);
};
