<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\DependencyInjection;

use Illuminate\Container\Container;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\VendorPatches\Command\GenerateCommand;

final class ContainerFactory
{
    /**
     * @api used in bin/vendor-patches.php
     */
    public function create(): Container
    {
        $container = new Container();

        // console
        $container->singleton(SymfonyStyle::class, static function (): SymfonyStyle {
            $arrayInput = new ArrayInput([]);
            $consoleOutput = new ConsoleOutput();
            return new SymfonyStyle($arrayInput, $consoleOutput);
        });

        // application
        $container->singleton(Application::class, static function (Container $container): Application {
            $application = new Application();

            $generateCommand = $container->make(GenerateCommand::class);
            $application->add($generateCommand);

            return $application;
        });

        // differ
        $container->singleton(
            UnifiedDiffOutputBuilder::class,
            static fn (): UnifiedDiffOutputBuilder => new UnifiedDiffOutputBuilder("--- Original\n+++ New\n", true)
        );

        $container->singleton(
            Differ::class,
            static fn (Container $container): Differ => new Differ($container->make(UnifiedDiffOutputBuilder::class))
        );

        return $container;
    }
}
