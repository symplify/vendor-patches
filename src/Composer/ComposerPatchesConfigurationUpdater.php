<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Composer;

use Migrify\VendorPatches\Json\JsonFileSystem;

final class ComposerPatchesConfigurationUpdater
{
    /**
     * @var JsonFileSystem
     */
    private $jsonFileSystem;

    public function __construct(JsonFileSystem $jsonFileSystem)
    {
        $this->jsonFileSystem = $jsonFileSystem;
    }

    /**
     * @param mixed[] $composerExtraPatches
     */
    public function updateComposerJson(array $composerExtraPatches): void
    {
        $patchComposerJsonArray = [
            'extra' => [
                'patches' => $composerExtraPatches,
            ],
        ];

        $composerJsonFilePath = getcwd() . '/composer.json';
        $this->jsonFileSystem->mergeArrayToJsonFile($composerJsonFilePath, $patchComposerJsonArray);
    }
}
