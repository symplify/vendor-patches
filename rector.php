<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPreparedSets(
        codeQuality: true,
        typeDeclarations: true,
        deadCode: true,
        phpunitCodeQuality: true,
        naming: true,
        privatization: true,
        earlyReturn: true,
    )
    ->withPhpSets()
    ->withImportNames()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests']);
// $rectorConfig->skip(['*/scoper.php', '*/Source/*', '*/Fixture/*']);
