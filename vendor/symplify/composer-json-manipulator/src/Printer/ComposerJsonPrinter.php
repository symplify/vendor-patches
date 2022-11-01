<?php

declare (strict_types=1);
namespace VendorPatches202211\Symplify\ComposerJsonManipulator\Printer;

use VendorPatches202211\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use VendorPatches202211\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use VendorPatches202211\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @api
 */
final class ComposerJsonPrinter
{
    /**
     * @var \Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager
     */
    private $jsonFileManager;
    public function __construct(JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }
    public function printToString(ComposerJson $composerJson) : string
    {
        return $this->jsonFileManager->encodeJsonToFileContent($composerJson->getJsonArray());
    }
    /**
     * @param string|\Symplify\SmartFileSystem\SmartFileInfo $targetFile
     */
    public function print(ComposerJson $composerJson, $targetFile) : void
    {
        if (\is_string($targetFile)) {
            $this->jsonFileManager->printComposerJsonToFilePath($composerJson, $targetFile);
            return;
        }
        $this->jsonFileManager->printJsonToFileInfo($composerJson->getJsonArray(), $targetFile);
    }
}
