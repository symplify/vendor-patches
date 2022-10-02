<?php

declare (strict_types=1);
namespace VendorPatches202210;

use VendorPatches202210\Symfony\Component\Console\Style\SymfonyStyle;
use VendorPatches202210\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VendorPatches202210\Symplify\ComposerJsonManipulator\ValueObject\Option;
use VendorPatches202210\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use VendorPatches202210\Symplify\PackageBuilder\Parameter\ParameterProvider;
use VendorPatches202210\Symplify\PackageBuilder\Reflection\PrivatesCaller;
use VendorPatches202210\Symplify\SmartFileSystem\SmartFileSystem;
use function VendorPatches202210\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::INLINE_SECTIONS, ['keywords']);
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('VendorPatches202210\Symplify\ComposerJsonManipulator\\', __DIR__ . '/../src');
    $services->set(SmartFileSystem::class);
    $services->set(PrivatesCaller::class);
    $services->set(ParameterProvider::class)->args([service('service_container')]);
    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)->factory([service(SymfonyStyleFactory::class), 'create']);
};
