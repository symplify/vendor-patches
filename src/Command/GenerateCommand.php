<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Command;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\VendorPatches\Composer\ComposerPatchesConfigurationUpdater;
use Symplify\VendorPatches\Console\GenerateCommandReporter;
use Symplify\VendorPatches\Differ\PatchDiffer;
use Symplify\VendorPatches\Finder\OldToNewFilesFinder;
use Symplify\VendorPatches\PatchFileFactory;
use Symplify\VendorPatches\VendorDirProvider;

final class GenerateCommand extends Command
{
    private const PATCHES_FILE_OPTION = 'patches-file';

    private const PATCHES_OUTPUT_OPTION = 'patches-folder';

    public function __construct(
        private readonly OldToNewFilesFinder $oldToNewFilesFinder,
        private readonly PatchDiffer $patchDiffer,
        private readonly ComposerPatchesConfigurationUpdater $composerPatchesConfigurationUpdater,
        private readonly PatchFileFactory $patchFileFactory,
        private readonly GenerateCommandReporter $generateCommandReporter,
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('generate');
        $this->setDescription('Generate patches from /vendor directory');
        $this->addOption(
            self::PATCHES_FILE_OPTION,
            null,
            InputOption::VALUE_OPTIONAL,
            'Path to the patches file, relative to project root'
        );

        $this->addOption(
            self::PATCHES_OUTPUT_OPTION,
            null,
            InputOption::VALUE_OPTIONAL,
            'Folder to output the patches to.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectVendorDirectory = $this->resolveProjectVendorDirectory();

        $oldAndNewFiles = $this->oldToNewFilesFinder->find($projectVendorDirectory);

        $composerExtraPatches = [];
        $addedPatchFilesByPackageName = [];

        $patchesOutputFolder = $input->getOption(self::PATCHES_OUTPUT_OPTION);
        if (is_string($patchesOutputFolder)) {
            $this->patchFileFactory->setOutputFolder($patchesOutputFolder);
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
                $this->symfonyStyle->note($message);
            } else {
                $message = sprintf('File "%s" was created', $patchFileRelativePath);
                $this->symfonyStyle->note($message);
            }

            FileSystem::write($patchFileAbsolutePath, $patchDiff);

            $addedPatchFilesByPackageName[$oldAndNewFile->getPackageName()][] = $patchFileRelativePath;
        }

        if ($composerExtraPatches !== []) {
            $patchesFilePath = $input->getOption(self::PATCHES_FILE_OPTION);

            if (is_string($patchesFilePath)) {
                $this->composerPatchesConfigurationUpdater->updatePatchesFileJsonAndPrint(
                    FileSystem::joinPaths(getcwd(), $patchesFilePath),
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
            $this->symfonyStyle->success($message);
        } else {
            $this->symfonyStyle->success('No new patches were added');
        }

        return self::SUCCESS;
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
