<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\DependencyInjection;

use Entropy\Container\Container;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

final class ContainerFactory
{
    public static function create(): Container
    {
        $container = new Container();

        $container->autodiscover(__DIR__ . '/../Command');

        // differ
        $container->service(
            UnifiedDiffOutputBuilder::class,
            static fn (): UnifiedDiffOutputBuilder => new UnifiedDiffOutputBuilder("--- Original\n+++ New\n", true)
        );

        $container->service(
            Differ::class,
            static fn (Container $container): Differ => new Differ($container->make(UnifiedDiffOutputBuilder::class))
        );

        return $container;
    }
}
