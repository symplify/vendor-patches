<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Console;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;

final class GenerateCommandReporter
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle
    ) {
    }

    public function reportIdenticalNewAndOldFile(OldAndNewFile $oldAndNewFileInfo): void
    {
        $message = sprintf(
            'Files "%s" and "%s" have the same content. Did you forgot to change it?',
            $oldAndNewFileInfo->getOldFilePath(),
            $oldAndNewFileInfo->getNewFilePath()
        );

        $this->symfonyStyle->warning($message);
    }
}
