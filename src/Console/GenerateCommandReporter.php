<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Console;

use Entropy\Console\Output\OutputPrinter;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;

final readonly class GenerateCommandReporter
{
    public function __construct(
        private OutputPrinter $outputPrinter
    ) {
    }

    public function reportIdenticalNewAndOldFile(OldAndNewFile $oldAndNewFile): void
    {
        $message = sprintf(
            'Files "%s" and "%s" have the same content. Did you forgot to change it?',
            $oldAndNewFile->getOldFilePath(),
            $oldAndNewFile->getNewFilePath()
        );

        $this->outputPrinter->orangeBackground($message);
    }
}
