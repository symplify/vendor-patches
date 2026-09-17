<?php

declare (strict_types=1);
namespace Symplify\VendorPatches\Command;

use VendorPatches202609\Entropy\Console\Contract\CommandInterface;
use VendorPatches202609\Entropy\Console\Enum\ExitCode;
use VendorPatches202609\Entropy\Console\Output\OutputPrinter;
use Symplify\VendorPatches\FileSystem\OldFilesFinder;
use Symplify\VendorPatches\VendorDirProvider;
final class CleanupCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \Entropy\Console\Output\OutputPrinter
     */
    private $outputPrinter;
    public function __construct(OutputPrinter $outputPrinter)
    {
        $this->outputPrinter = $outputPrinter;
    }
    /**
     * @return \Entropy\Console\Enum\ExitCode::*
     */
    public function run() : int
    {
        $projectVendorDirectory = $this->resolveProjectVendorDirectory();
        $oldFilePaths = OldFilesFinder::findOldFiles($projectVendorDirectory);
        $deletedCount = 0;
        foreach ($oldFilePaths as $oldFilePath) {
            if (!\unlink($oldFilePath)) {
                $this->outputPrinter->redBackground(\sprintf('Failed to remove "%s"', $oldFilePath));
                return ExitCode::ERROR;
            }
            $this->outputPrinter->yellow(\sprintf('File "%s" was removed', $oldFilePath));
            ++$deletedCount;
        }
        if ($deletedCount > 0) {
            $this->outputPrinter->greenBackground(\sprintf('%d *.old file(s) removed', $deletedCount));
        } else {
            $this->outputPrinter->greenBackground('No *.old files were found');
        }
        return ExitCode::SUCCESS;
    }
    public function getName() : string
    {
        return 'cleanup';
    }
    public function getDescription() : string
    {
        return 'Remove all *.old backup files from /vendor directory';
    }
    private function resolveProjectVendorDirectory() : string
    {
        $projectVendorDirectory = \getcwd() . '/vendor';
        if (\file_exists($projectVendorDirectory)) {
            return $projectVendorDirectory;
        }
        return VendorDirProvider::provide();
    }
}
