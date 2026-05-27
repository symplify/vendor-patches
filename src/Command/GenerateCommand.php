<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Entropy\Utils\FileSystem;
use Symplify\VendorPatches\Composer\ComposerPatchesConfigurationUpdater;
use Symplify\VendorPatches\Console\GenerateCommandReporter;
use Symplify\VendorPatches\Differ\PatchDiffer;
use Symplify\VendorPatches\Finder\OldToNewFilesFinder;
use Symplify\VendorPatches\PatchFileFactory;
use Symplify\VendorPatches\VendorDirProvider;

final readonly class GenerateCommand implements CommandInterface
{
    public function __construct(
        private OldToNewFilesFinder $oldToNewFilesFinder,
        private PatchDiffer $patchDiffer,
        private ComposerPatchesConfigurationUpdater $composerPatchesConfigurationUpdater,
        private PatchFileFactory $patchFileFactory,
        private GenerateCommandReporter $generateCommandReporter,
        private OutputPrinter $outputPrinter,
    ) {
    }

    /**
     * @param string|null $patchesFile Path to the patches file, relative to project root
     * @param string|null $patchesOutput Folder to output the patches to.
     * @param string|null $patchesFolder Alias for --patches-output.
     * @param bool|null $resolveFromDirectory Resolve package name from path in vendor/ instead of the package's composer.json. This is useful for private repositories where the name in the repository differs from the name in composer.json.
     *
     * @return \Entropy\Console\Enum\ExitCode::*
     */
    public function run(
        ?string $patchesFile = null,
        ?string $patchesOutput = null,
        // alias for --patches-output, to avoid confusion with the --patches-file option
        ?string $patchesFolder = null,
        ?bool $resolveFromDirectory = false
    ): int {
        $projectVendorDirectory = $this->resolveProjectVendorDirectory();

        $oldAndNewFiles = $this->oldToNewFilesFinder->find($projectVendorDirectory, (bool) $resolveFromDirectory);

        $composerExtraPatches = [];
        $addedPatchFilesByPackageName = [];

        $patchesOutput ??= $patchesFolder;

        if (is_string($patchesOutput)) {
            $this->patchFileFactory->setOutputFolder($patchesOutput);
        }

        foreach ($oldAndNewFiles as $oldAndNewFile) {
            if ($oldAndNewFile->areContentsIdentical()) {
                $this->generateCommandReporter->reportIdenticalNewAndOldFile($oldAndNewFile);
                continue;
            }

            // write into patches file
            $patchFileRelativePath = $this->patchFileFactory->createPatchFilePath(
                $oldAndNewFile,
                $projectVendorDirectory
            );
            $composerExtraPatches[$oldAndNewFile->getPackageName()][] = $patchFileRelativePath;

            $patchFileAbsolutePath = dirname($projectVendorDirectory) . DIRECTORY_SEPARATOR . $patchFileRelativePath;

            // dump the patch
            $patchDiff = $this->patchDiffer->diff($oldAndNewFile);

            if (is_file($patchFileAbsolutePath)) {
                $message = sprintf('File "%s" was updated', $patchFileRelativePath);
                $this->outputPrinter->yellow($message);
            } else {
                $message = sprintf('File "%s" was created', $patchFileRelativePath);
                $this->outputPrinter->yellow($message);
            }

            FileSystem::write($patchFileAbsolutePath, $patchDiff);

            $addedPatchFilesByPackageName[$oldAndNewFile->getPackageName()][] = $patchFileRelativePath;
        }

        if ($composerExtraPatches !== []) {
            if (is_string($patchesFile)) {
                // remove starting '/' if present
                $patchesFile = ltrim($patchesFile, '/\\');

                $absolutePatchesFilePath = getcwd() . '/' . $patchesFile;

                $this->composerPatchesConfigurationUpdater->updatePatchesFileJsonAndPrint(
                    $absolutePatchesFilePath,
                    $composerExtraPatches
                );
            } else {
                $this->composerPatchesConfigurationUpdater->updateComposerJsonAndPrint(
                    getcwd() . '/composer.json',
                    $composerExtraPatches
                );
            }
        }

        if ($addedPatchFilesByPackageName !== []) {
            $message = sprintf('Great! %d new patch files added', count($addedPatchFilesByPackageName));
            $this->outputPrinter->greenBackground($message);
        } else {
            $this->outputPrinter->greenBackground('No new patches were added');
        }

        return ExitCode::SUCCESS;
    }

    public function getName(): string
    {
        return 'generate';
    }

    public function getDescription(): string
    {
        return 'Generate patches from /vendor directory';
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
