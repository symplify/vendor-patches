<?php

declare (strict_types=1);
namespace Symplify\VendorPatches\Composer;

use VendorPatches202502\Nette\Utils\FileSystem;
use VendorPatches202502\Nette\Utils\Json;
use Symplify\VendorPatches\Utils\ParametersMerger;
/**
 * @see \Symplify\VendorPatches\Tests\Composer\ComposerPatchesConfigurationUpdater\ComposerPatchesConfigurationUpdaterTest
 */
final class ComposerPatchesConfigurationUpdater
{
    /**
     * @readonly
     * @var \Symplify\VendorPatches\Utils\ParametersMerger
     */
    private $parametersMerger;
    public function __construct(ParametersMerger $parametersMerger)
    {
        $this->parametersMerger = $parametersMerger;
    }
    /**
     * @api
     * @param mixed[] $composerExtraPatches
     * @return mixed[]
     */
    public function updatePatchesFileJson(string $patchesFilePath, array $composerExtraPatches) : array
    {
        $patchesFileContents = FileSystem::read($patchesFilePath);
        $patchesFileJson = Json::decode($patchesFileContents, Json::FORCE_ARRAY);
        return $this->parametersMerger->merge($patchesFileJson, ['patches' => $composerExtraPatches]);
    }
    /**
     * @api
     * @param mixed[] $composerExtraPatches
     * @return mixed[]
     */
    public function updateComposerJson(string $composerJsonFilePath, array $composerExtraPatches) : array
    {
        $composerFileContents = FileSystem::read($composerJsonFilePath);
        $composerJson = Json::decode($composerFileContents, Json::FORCE_ARRAY);
        // merge "extra" section - deep merge is needed, so original patches are included
        if (isset($composerJson['extra'])) {
            $composerJson['extra'] = $this->parametersMerger->merge($composerJson['extra'], ['patches' => $composerExtraPatches]);
            return $composerJson;
        }
        // new, put the patches last
        $composerJson['extra'] = ['patches' => $composerExtraPatches];
        return $composerJson;
    }
    /**
     * @param mixed[] $composerExtraPatches
     */
    public function updateComposerJsonAndPrint(string $composerJsonFilePath, array $composerExtraPatches) : void
    {
        $composerJson = $this->updateComposerJson($composerJsonFilePath, $composerExtraPatches);
        // print composer.json
        $composerJsonFileContents = Json::encode($composerJson, Json::PRETTY);
        FileSystem::write($composerJsonFilePath, $composerJsonFileContents, null);
    }
    /**
     * @param mixed[] $composerExtraPatches
     */
    public function updatePatchesFileJsonAndPrint(string $patchesFilePath, array $composerExtraPatches) : void
    {
        $patchesFileJson = $this->updatePatchesFileJson($patchesFilePath, $composerExtraPatches);
        $patchesFileContents = Json::encode($patchesFileJson, Json::PRETTY);
        FileSystem::write($patchesFilePath, $patchesFileContents, null);
    }
}
