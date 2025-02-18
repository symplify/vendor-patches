<?php

declare (strict_types=1);
namespace Symplify\VendorPatches\Command;

use VendorPatches202502\Nette\Utils\FileSystem;
use VendorPatches202502\Symfony\Component\Console\Command\Command;
use VendorPatches202502\Symfony\Component\Console\Input\InputInterface;
use VendorPatches202502\Symfony\Component\Console\Input\InputOption;
use VendorPatches202502\Symfony\Component\Console\Output\OutputInterface;
use VendorPatches202502\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\VendorPatches\Composer\ComposerPatchesConfigurationUpdater;
use Symplify\VendorPatches\Console\GenerateCommandReporter;
use Symplify\VendorPatches\Differ\PatchDiffer;
use Symplify\VendorPatches\Finder\OldToNewFilesFinder;
use Symplify\VendorPatches\PatchFileFactory;
use Symplify\VendorPatches\VendorDirProvider;
final class GenerateCommand extends Command
{
    /**
     * @readonly
     * @var \Symplify\VendorPatches\Finder\OldToNewFilesFinder
     */
    private $oldToNewFilesFinder;
    /**
     * @readonly
     * @var \Symplify\VendorPatches\Differ\PatchDiffer
     */
    private $patchDiffer;
    /**
     * @readonly
     * @var \Symplify\VendorPatches\Composer\ComposerPatchesConfigurationUpdater
     */
    private $composerPatchesConfigurationUpdater;
    /**
     * @readonly
     * @var \Symplify\VendorPatches\PatchFileFactory
     */
    private $patchFileFactory;
    /**
     * @readonly
     * @var \Symplify\VendorPatches\Console\GenerateCommandReporter
     */
    private $generateCommandReporter;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    private const PATCHES_FILE_OPTION = 'patches-file';
    public function __construct(OldToNewFilesFinder $oldToNewFilesFinder, PatchDiffer $patchDiffer, ComposerPatchesConfigurationUpdater $composerPatchesConfigurationUpdater, PatchFileFactory $patchFileFactory, GenerateCommandReporter $generateCommandReporter, SymfonyStyle $symfonyStyle)
    {
        $this->oldToNewFilesFinder = $oldToNewFilesFinder;
        $this->patchDiffer = $patchDiffer;
        $this->composerPatchesConfigurationUpdater = $composerPatchesConfigurationUpdater;
        $this->patchFileFactory = $patchFileFactory;
        $this->generateCommandReporter = $generateCommandReporter;
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('generate');
        $this->setDescription('Generate patches from /vendor directory');
        $this->addOption(self::PATCHES_FILE_OPTION, null, InputOption::VALUE_OPTIONAL, 'Path to the patches file, relative to project root');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $projectVendorDirectory = $this->resolveProjectVendorDirectory();
        $oldAndNewFiles = $this->oldToNewFilesFinder->find($projectVendorDirectory);
        $composerExtraPatches = [];
        $addedPatchFilesByPackageName = [];
        foreach ($oldAndNewFiles as $oldAndNewFile) {
            if ($oldAndNewFile->areContentsIdentical()) {
                $this->generateCommandReporter->reportIdenticalNewAndOldFile($oldAndNewFile);
                continue;
            }
            // write into patches file
            $patchFileRelativePath = $this->patchFileFactory->createPatchFilePath($oldAndNewFile, $projectVendorDirectory);
            $composerExtraPatches[$oldAndNewFile->getPackageName()][] = $patchFileRelativePath;
            $patchFileAbsolutePath = \dirname($projectVendorDirectory) . \DIRECTORY_SEPARATOR . $patchFileRelativePath;
            // dump the patch
            $patchDiff = $this->patchDiffer->diff($oldAndNewFile);
            if (\is_file($patchFileAbsolutePath)) {
                $message = \sprintf('File "%s" was updated', $patchFileRelativePath);
                $this->symfonyStyle->note($message);
            } else {
                $message = \sprintf('File "%s" was created', $patchFileRelativePath);
                $this->symfonyStyle->note($message);
            }
            FileSystem::write($patchFileAbsolutePath, $patchDiff);
            $addedPatchFilesByPackageName[$oldAndNewFile->getPackageName()][] = $patchFileRelativePath;
        }
        if ($composerExtraPatches !== []) {
            $patchesFilePath = $input->getOption(self::PATCHES_FILE_OPTION);
            if (\is_string($patchesFilePath)) {
                $this->composerPatchesConfigurationUpdater->updatePatchesFileJsonAndPrint(FileSystem::joinPaths(\getcwd(), $patchesFilePath), $composerExtraPatches);
            } else {
                $this->composerPatchesConfigurationUpdater->updateComposerJsonAndPrint(\getcwd() . '/composer.json', $composerExtraPatches);
            }
        }
        if ($addedPatchFilesByPackageName !== []) {
            $message = \sprintf('Great! %d new patch files added', \count($addedPatchFilesByPackageName));
            $this->symfonyStyle->success($message);
        } else {
            $this->symfonyStyle->success('No new patches were added');
        }
        return self::SUCCESS;
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
