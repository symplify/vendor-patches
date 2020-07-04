<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Console\Command;

use Migrify\VendorPatches\Composer\ComposerPatchesConfigurationUpdater;
use Migrify\VendorPatches\Differ\PatchDiffer;
use Migrify\VendorPatches\Finder\OldToNewFilesFinder;
use Migrify\VendorPatches\ValueObject\OldAndNewFileInfo;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Composer\StaticVendorDirProvider;
use Symplify\PackageBuilder\Console\ShellCode;

final class GenerateCommand extends Command
{
    /**
     * @var OldToNewFilesFinder
     */
    private $oldToNewFilesFinder;

    /**
     * @var PatchDiffer
     */
    private $patchDiffer;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ComposerPatchesConfigurationUpdater
     */
    private $composerPatchesConfigurationUpdater;

    public function __construct(
        OldToNewFilesFinder $vendorFilesFinder,
        PatchDiffer $patchDiffer,
        ComposerPatchesConfigurationUpdater $composerPatchesConfigurationUpdater,
        SymfonyStyle $symfonyStyle
    ) {
        $this->oldToNewFilesFinder = $vendorFilesFinder;
        $this->patchDiffer = $patchDiffer;
        $this->symfonyStyle = $symfonyStyle;
        $this->composerPatchesConfigurationUpdater = $composerPatchesConfigurationUpdater;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('generate');
        $this->setDescription('Generate patches from /vendor directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vendorDirectory = StaticVendorDirProvider::provide();

        $oldAndNewFileInfos = $this->oldToNewFilesFinder->find($vendorDirectory);

        $composerExtraPatches = [];
        $addedPatchFilesByPackageName = [];

        foreach ($oldAndNewFileInfos as $oldAndNewFileInfo) {
            if ($oldAndNewFileInfo->isContentIdentical()) {
                $this->reportIdenticalNewAndOldFile($oldAndNewFileInfo);
                continue;
            }

            // write into patches file
            $patchFileRelativePath = $this->createPatchFilePath($oldAndNewFileInfo, $vendorDirectory);
            $composerExtraPatches[$oldAndNewFileInfo->getPackageName()][] = $patchFileRelativePath;

            $patchFileAbsolutePath = dirname($vendorDirectory) . DIRECTORY_SEPARATOR . $patchFileRelativePath;

            // dump the patch
            $patchDiff = $this->patchDiffer->diff($oldAndNewFileInfo);

            if ($this->doesPatchAlreadyExist($patchFileAbsolutePath, $patchDiff)) {
                $message = sprintf('Patch file "%s" with same content is already created', $patchFileRelativePath);
                $this->symfonyStyle->note($message);
                continue;
            }

            if (is_file($patchFileAbsolutePath)) {
                $this->symfonyStyle->note(sprintf('File "%s" was updated', $patchFileRelativePath));
            } else {
                $this->symfonyStyle->note(sprintf('File "%s" was created', $patchFileRelativePath));
            }

            FileSystem::write($patchFileAbsolutePath, $patchDiff);

            $addedPatchFilesByPackageName[$oldAndNewFileInfo->getPackageName()][] = $patchFileRelativePath;
        }

        $this->composerPatchesConfigurationUpdater->updateComposerJson($composerExtraPatches);

        if ($addedPatchFilesByPackageName !== []) {
            $message = sprintf('Great! %d new patch files added', count($addedPatchFilesByPackageName));
            $this->symfonyStyle->success($message);
        } else {
            $this->symfonyStyle->success('No new patches were added');
        }

        return ShellCode::SUCCESS;
    }

    private function createPatchFilePath(OldAndNewFileInfo $oldAndNewFileInfo, string $vendorDirectory): string
    {
        $newFileInfo = $oldAndNewFileInfo->getNewFileInfo();

        $inVendorRelativeFilePath = $newFileInfo->getRelativeFilePathFromDirectory($vendorDirectory);

        $relativeFilePathWithoutSuffix = Strings::lower($inVendorRelativeFilePath);
        $pathFileName = Strings::webalize($relativeFilePathWithoutSuffix) . '.patch';

        return 'patches' . DIRECTORY_SEPARATOR . $pathFileName;
    }

    private function doesPatchAlreadyExist(string $patchFileAbsolutePath, string $diff): bool
    {
        if (! is_file($patchFileAbsolutePath)) {
            return false;
        }

        return FileSystem::read($patchFileAbsolutePath) === $diff;
    }

    private function reportIdenticalNewAndOldFile(OldAndNewFileInfo $oldAndNewFileInfo): void
    {
        $message = sprintf(
            'Files "%s" and "%s" have the same content. Did you forgot to change it?',
            $oldAndNewFileInfo->getOldFileRelativePath(),
            $oldAndNewFileInfo->getNewFileRelativePath()
        );

        $this->symfonyStyle->warning($message);
    }
}
