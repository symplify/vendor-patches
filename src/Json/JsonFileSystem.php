<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Json;

use Nette\Utils\Arrays;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Symplify\SmartFileSystem\FileSystemGuard;

final class JsonFileSystem
{
    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    public function __construct(FileSystemGuard $fileSystemGuard)
    {
        $this->fileSystemGuard = $fileSystemGuard;
    }

    public function loadFilePathToJson(string $filePath): array
    {
        $this->fileSystemGuard->ensureFileExists($filePath, __METHOD__);

        $fileContent = FileSystem::read($filePath);
        return Json::decode($fileContent, Json::FORCE_ARRAY);
    }

    public function writeJsonToFilePath(array $jsonArray, string $filePath): void
    {
        $jsonContent = Json::encode($jsonArray, Json::PRETTY) . PHP_EOL;
        FileSystem::write($filePath, $jsonContent);
    }

    public function mergeArrayToJsonFile(string $filePath, array $newJsonArray): void
    {
        $jsonArray = $this->loadFilePathToJson($filePath);

        $newComposerJsonArray = Arrays::mergeTree($jsonArray, $newJsonArray);

        $this->writeJsonToFilePath($newComposerJsonArray, $filePath);
    }
}
