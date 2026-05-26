<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Symfony\Component\Finder\Finder;
use Symplify\VendorPatches\VendorDirProvider;

final readonly class CleanupCommand implements CommandInterface
{
    public function __construct(
        private OutputPrinter $outputPrinter,
    ) {
    }

    /**
     * @return \Entropy\Console\Enum\ExitCode::*
     */
    public function run(): int
    {
        $projectVendorDirectory = $this->resolveProjectVendorDirectory();

        $finder = Finder::create()
            ->in($projectVendorDirectory)
            ->files()
            ->exclude('composer/')
            ->exclude('ocramius/')
            ->name('*.old');

        $deletedCount = 0;

        foreach ($finder as $fileInfo) {
            $filePath = $fileInfo->getPathname();

            if (! unlink($filePath)) {
                $this->outputPrinter->redBackground(sprintf('Failed to remove "%s"', $filePath));
                return ExitCode::ERROR;
            }

            $this->outputPrinter->yellow(sprintf('File "%s" was removed', $filePath));
            ++$deletedCount;
        }

        if ($deletedCount > 0) {
            $this->outputPrinter->greenBackground(sprintf('%d *.old file(s) removed', $deletedCount));
        } else {
            $this->outputPrinter->greenBackground('No *.old files were found');
        }

        return ExitCode::SUCCESS;
    }

    public function getName(): string
    {
        return 'cleanup';
    }

    public function getDescription(): string
    {
        return 'Remove all *.old backup files from /vendor directory';
    }

    private function resolveProjectVendorDirectory(): string
    {
        $projectVendorDirectory = getcwd() . '/vendor';
        if (file_exists($projectVendorDirectory)) {
            return $projectVendorDirectory;
        }

        return VendorDirProvider::provide();
    }
}
