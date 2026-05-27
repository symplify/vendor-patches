<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;

final readonly class BackupCommand implements CommandInterface
{
    public function __construct(
        private OutputPrinter $outputPrinter,
    ) {
    }

    /**
     * @param string ...$files Vendor files to back up by copying them to "<path>.orig"
     *
     * @return \Entropy\Console\Enum\ExitCode::*
     */
    public function run(string ...$files): int
    {
        if ($files === []) {
            $this->outputPrinter->redBackground('No files provided. Pass one or more vendor file paths to back up.');
            return ExitCode::ERROR;
        }

        $backupCount = 0;

        foreach ($files as $file) {
            if (! is_file($file)) {
                $this->outputPrinter->redBackground(sprintf('File "%s" was not found', $file));
                return ExitCode::ERROR;
            }

            $backupFile = $file . '.orig';

            if (is_file($backupFile)) {
                $this->outputPrinter->orangeBackground(
                    sprintf('Backup "%s" already exists, skipping', $backupFile)
                );
                continue;
            }

            if (! copy($file, $backupFile)) {
                $this->outputPrinter->redBackground(sprintf('Failed to create backup "%s"', $backupFile));
                return ExitCode::ERROR;
            }

            $this->outputPrinter->yellow(sprintf('File "%s" was backed up', $backupFile));
            ++$backupCount;
        }

        if ($backupCount > 0) {
            $this->outputPrinter->greenBackground(sprintf('%d backup file(s) created', $backupCount));
        } else {
            $this->outputPrinter->greenBackground('No new backup files were created');
        }

        return ExitCode::SUCCESS;
    }

    public function getName(): string
    {
        return 'backup';
    }

    public function getDescription(): string
    {
        return 'Create *.orig backup copies of given /vendor files';
    }
}
