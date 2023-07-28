<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\DependencyInjection;

use Illuminate\Container\Container;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ContainerFactory
{
    public function create(): Container
    {
        $container = new Container();
        $container->singleton(SymfonyStyle::class, function (): SymfonyStyle {
            return new SymfonyStyle(new ArrayInput([]), new ConsoleOutput());
        });

        // differ
        $container->singleton(UnifiedDiffOutputBuilder::class, function () {
            return new UnifiedDiffOutputBuilder("--- Original\n+++ New\n", true);
        });

        $container->singleton(Differ::class, function (Container $container) {
            return new Differ($container->make(UnifiedDiffOutputBuilder::class));
        });

        return $container;
    }
}
