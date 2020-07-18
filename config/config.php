<?php

declare(strict_types=1);

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileSystem;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Migrify\VendorPatches\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/HttpKernel/*', __DIR__ . '/../src/ValueObject/*']);

    $services->set(UnifiedDiffOutputBuilder::class)
        ->args(['$addLineNumbers' => true]);

    $services->set(Differ::class)
        ->args(['$outputBuilder' => ref(UnifiedDiffOutputBuilder::class)]);

    $services->set(FinderSanitizer::class);

    $services->set(FileSystemGuard::class);

    $services->set(SymfonyStyleFactory::class);

    $services->set(SymfonyStyle::class)
        ->factory([ref(SymfonyStyleFactory::class), 'create']);

    $services->set(SmartFileSystem::class);
};
