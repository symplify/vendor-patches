<?php

declare (strict_types=1);
namespace Symplify\VendorPatches\DependencyInjection;

use VendorPatches202502\Illuminate\Container\Container;
use VendorPatches202502\SebastianBergmann\Diff\Differ;
use VendorPatches202502\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use VendorPatches202502\Symfony\Component\Console\Application;
use VendorPatches202502\Symfony\Component\Console\Input\ArrayInput;
use VendorPatches202502\Symfony\Component\Console\Output\ConsoleOutput;
use VendorPatches202502\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\VendorPatches\Command\GenerateCommand;
final class ContainerFactory
{
    /**
     * @api used in bin/vendor-patches.php
     */
    public function create() : Container
    {
        $container = new Container();
        // console
        $container->singleton(SymfonyStyle::class, static function () : SymfonyStyle {
            $arrayInput = new ArrayInput([]);
            $consoleOutput = new ConsoleOutput();
            return new SymfonyStyle($arrayInput, $consoleOutput);
        });
        // application
        $container->singleton(Application::class, static function (Container $container) : Application {
            $application = new Application();
            $generateCommand = $container->make(GenerateCommand::class);
            $application->add($generateCommand);
            // hide default commands
            $application->get('completion')->setHidden(\true);
            $application->get('help')->setHidden(\true);
            $application->get('list')->setHidden(\true);
            return $application;
        });
        // differ
        $container->singleton(UnifiedDiffOutputBuilder::class, static function () : UnifiedDiffOutputBuilder {
            return new UnifiedDiffOutputBuilder("--- Original\n+++ New\n", \true);
        });
        $container->singleton(Differ::class, static function (Container $container) : Differ {
            return new Differ($container->make(UnifiedDiffOutputBuilder::class));
        });
        return $container;
    }
}
