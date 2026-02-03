<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPreparedSets(
        codeQuality: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        codingStyle: true,
        deadCode: true,
        phpunitCodeQuality: true,
        naming: true,
        privatization: true,
        earlyReturn: true,
    )
    ->withPhpSets()
    ->withRootFiles()
    ->withImportNames()
    ->withPaths([__DIR__ . '/bin', __DIR__ . '/src', __DIR__ . '/tests']);
