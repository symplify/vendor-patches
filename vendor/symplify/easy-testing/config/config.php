<?php

declare (strict_types=1);
namespace VendorPatches20220611;

use VendorPatches20220611\Symfony\Component\Console\Application;
use VendorPatches20220611\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VendorPatches20220611\Symplify\EasyTesting\Command\ValidateFixtureSkipNamingCommand;
use function VendorPatches20220611\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('VendorPatches20220611\Symplify\EasyTesting\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/DataProvider', __DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);
    // console
    $services->set(Application::class)->call('add', [service(ValidateFixtureSkipNamingCommand::class)]);
};
