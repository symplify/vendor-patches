<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Command;

use Migrify\VendorPatches\Composer\ComposerPatchesConfigurationUpdater;
use Migrify\VendorPatches\Differ\PatchDiffer;
use Migrify\VendorPatches\Finder\OldToNewFilesFinder;
use Migrify\VendorPatches\ValueObject\OldAndNewFileInfo;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Composer\VendorDirProvider;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileSystem;

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

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var VendorDirProvider
     */
    private $vendorDirProvider;

    public function __construct(
        OldToNewFilesFinder $oldToNewFilesFinder,
        PatchDiffer $patchDiffer,
        ComposerPatchesConfigurationUpdater $composerPatchesConfigurationUpdater,
        SymfonyStyle $symfonyStyle,
        SmartFileSystem $smartFileSystem,
        VendorDirProvider $vendorDirProvider
    ) {
        $this->oldToNewFilesFinder = $oldToNewFilesFinder;
        $this->patchDiffer = $patchDiffer;
        $this->symfonyStyle = $symfonyStyle;
        $this->composerPatchesConfigurationUpdater = $composerPatchesConfigurationUpdater;
        $this->smartFileSystem = $smartFileSystem;

        parent::__construct();

        $this->vendorDirProvider = $vendorDirProvider;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generate patches from /vendor directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vendorDirectory = $this->vendorDirProvider->provide();
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
                $message = sprintf('File "%s" was updated', $patchFileRelativePath);
                $this->symfonyStyle->note($message);
            } else {
                $message = sprintf('File "%s" was created', $patchFileRelativePath);
                $this->symfonyStyle->note($message);
            }

            $this->smartFileSystem->dumpFile($patchFileAbsolutePath, $patchDiff);

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

        return $this->smartFileSystem->readFile($patchFileAbsolutePath) === $diff;
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
