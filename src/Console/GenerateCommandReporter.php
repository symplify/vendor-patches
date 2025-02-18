<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Console;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;

final readonly class GenerateCommandReporter
{
    public function __construct(
        private SymfonyStyle $symfonyStyle
    ) {
    }

    public function reportIdenticalNewAndOldFile(OldAndNewFile $oldAndNewFile): void
    {
        $message = sprintf(
            'Files "%s" and "%s" have the same content. Did you forgot to change it?',
            $oldAndNewFile->getOldFilePath(),
            $oldAndNewFile->getNewFilePath()
        );

        $this->symfonyStyle->warning($message);
    }
}
