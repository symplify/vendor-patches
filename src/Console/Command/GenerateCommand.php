<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Console\Command;

use Migrify\VendorPatches\Composer\PackageNameResolver;
use Migrify\VendorPatches\Differ\PatchDiffer;
use Migrify\VendorPatches\Finder\VendorFilesFinder;
use Migrify\VendorPatches\ValueObject\Option;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class GenerateCommand extends Command
{
    /**
     * @var VendorFilesFinder
     */
    private $vendorFilesFinder;

    /**
     * @var PatchDiffer
     */
    private $patchDiffer;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var PackageNameResolver
     */
    private $packageNameResolver;

    public function __construct(
        VendorFilesFinder $vendorFilesFinder,
        PatchDiffer $patchDiffer,
        SymfonyStyle $symfonyStyle,
        PackageNameResolver $packageNameResolver
    ) {
        $this->vendorFilesFinder = $vendorFilesFinder;
        $this->patchDiffer = $patchDiffer;
        $this->symfonyStyle = $symfonyStyle;
        $this->packageNameResolver = $packageNameResolver;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('generate');
        $this->setDescription('Generate patches from 2 provided directories, first vendor, than changed vendor');
        $this->addArgument(Option::VENDOR_DIRECTORY, InputArgument::REQUIRED);
        $this->addArgument(Option::CHANGED_VENDOR_DIRECTORY, InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vendorDirectory = (string) $input->getArgument(Option::VENDOR_DIRECTORY);
        $changedDirectory = (string) $input->getArgument(Option::CHANGED_VENDOR_DIRECTORY);

        $originalVendorFiles = $this->vendorFilesFinder->find($vendorDirectory);
        $changedVendorFiles = $this->vendorFilesFinder->find($changedDirectory);

        $composerExtraPatches = [];

        foreach ($originalVendorFiles as $relativeVendorFilePath => $originalVendorFile) {
            if ($this->shouldSkipFile($changedVendorFiles, $relativeVendorFilePath, $originalVendorFile)) {
                continue;
            }

            $changedVendorFile = $changedVendorFiles[$relativeVendorFilePath];

            // write into patches file
            $relativePathFileName = $this->createPathFilePath($relativeVendorFilePath);
            $absolutePathFilename = getcwd() . $relativeVendorFilePath;

            $packageName = $this->packageNameResolver->resolveFromFileInfo($originalVendorFile);
            $composerExtraPatches[$packageName][] = $relativeVendorFilePath;

            // dump the patch
            $diff = $this->patchDiffer->diff($originalVendorFile, $changedVendorFile);

            FileSystem::write($absolutePathFilename, $diff);

            $this->symfonyStyle->note(sprintf('File "%s" was created', $relativePathFileName));
        }

        $this->updateComposerJson($composerExtraPatches);

        // @todo update composer.json with extra patches

        $this->symfonyStyle->success('Patching done');

        return ShellCode::SUCCESS;
    }

    /**
     * @param SmartFileInfo[] $changedVendorFiles
     */
    private function shouldSkipFile(
        array $changedVendorFiles,
        string $relativeVendorFilePath,
        SmartFileInfo $originalVendorFile
    ): bool {
        // file must be located in both dirs
        if (! isset($changedVendorFiles[$relativeVendorFilePath])) {
            return true;
        }

        $changedVendorFile = $changedVendorFiles[$relativeVendorFilePath];

        // content is the same → nothing was changed
        return $changedVendorFile->getContents() === $originalVendorFile->getContents();
    }

    private function createPathFilePath(string $relativeVendorFilePath): string
    {
        $relativeFilePathWithoutSuffix = Strings::before($relativeVendorFilePath, '.php');

        assert(is_string($relativeFilePathWithoutSuffix));

        $pathFileName = Strings::webalize($relativeFilePathWithoutSuffix) . '.patch';

        return 'patches/' . $pathFileName;
    }

    /**
     * @param mixed[] $composerExtraPatches
     */
    private function updateComposerJson(array $composerExtraPatches): void
    {
        $composerJsonContent = [
            'config' => [
                'preffered-install' => 'source',
            ],
            'extra' => [
                'patches' => $composerExtraPatches,
            ],
        ];

        $composerExtraPatchContent = Json::encode($composerJsonContent, Json::PRETTY);

        // @todo write it down
    }
}
