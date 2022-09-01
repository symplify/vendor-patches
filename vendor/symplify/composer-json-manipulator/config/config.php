<?php

declare (strict_types=1);
namespace VendorPatches202209;

use VendorPatches202209\Symfony\Component\Console\Style\SymfonyStyle;
use VendorPatches202209\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VendorPatches202209\Symplify\ComposerJsonManipulator\ValueObject\Option;
use VendorPatches202209\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use VendorPatches202209\Symplify\PackageBuilder\Parameter\ParameterProvider;
use VendorPatches202209\Symplify\PackageBuilder\Reflection\PrivatesCaller;
use VendorPatches202209\Symplify\SmartFileSystem\SmartFileSystem;
use function VendorPatches202209\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::INLINE_SECTIONS, ['keywords']);
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('VendorPatches202209\Symplify\ComposerJsonManipulator\\', __DIR__ . '/../src');
    $services->set(SmartFileSystem::class);
    $services->set(PrivatesCaller::class);
    $services->set(ParameterProvider::class)->args([service('service_container')]);
    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)->factory([service(SymfonyStyleFactory::class), 'create']);
};
