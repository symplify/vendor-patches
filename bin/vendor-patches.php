<?php

declare(strict_types=1);

use Entropy\Console\ConsoleApplication;
use Symplify\VendorPatches\DependencyInjection\ContainerFactory;

$possibleAutoloadPaths = [
    __DIR__ . '/../autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../../../vendor/autoload.php',
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (! file_exists($possibleAutoloadPath)) {
        continue;
    }

    require_once $possibleAutoloadPath;
}

$scoperAutoloadFilepath = __DIR__ . '/../vendor/scoper-autoload.php';
if (file_exists($scoperAutoloadFilepath)) {
    require_once $scoperAutoloadFilepath;
}

$container = ContainerFactory::create();

$application = $container->make(ConsoleApplication::class);

$exitCode = $application->run($argv);
exit($exitCode);
