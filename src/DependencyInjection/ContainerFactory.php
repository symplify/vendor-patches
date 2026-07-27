<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\DependencyInjection;

use Entropy\Container\Container;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\StrictUnifiedDiffOutputBuilder;

final class ContainerFactory
{
    public static function create(): Container
    {
        $container = new Container();

        $container->autodiscover(__DIR__ . '/../Command');

        // differ
        $container->service(
            StrictUnifiedDiffOutputBuilder::class,
            static fn (): StrictUnifiedDiffOutputBuilder => new StrictUnifiedDiffOutputBuilder([
                'fromFile' => 'Original',
                'toFile' => 'New',
            ])
        );

        $container->service(
            Differ::class,
            static fn (Container $container): Differ => new Differ($container->make(StrictUnifiedDiffOutputBuilder::class))
        );

        return $container;
    }
}
