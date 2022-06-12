<?php

declare (strict_types=1);
namespace VendorPatches20220612;

use VendorPatches20220612\Symfony\Component\Console\Style\SymfonyStyle;
use VendorPatches20220612\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VendorPatches20220612\Symplify\ComposerJsonManipulator\ValueObject\Option;
use VendorPatches20220612\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use VendorPatches20220612\Symplify\PackageBuilder\Parameter\ParameterProvider;
use VendorPatches20220612\Symplify\PackageBuilder\Reflection\PrivatesCaller;
use VendorPatches20220612\Symplify\SmartFileSystem\SmartFileSystem;
use function VendorPatches20220612\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::INLINE_SECTIONS, ['keywords']);
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('VendorPatches20220612\Symplify\ComposerJsonManipulator\\', __DIR__ . '/../src');
    $services->set(SmartFileSystem::class);
    $services->set(PrivatesCaller::class);
    $services->set(ParameterProvider::class)->args([service('service_container')]);
    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)->factory([service(SymfonyStyleFactory::class), 'create']);
};
