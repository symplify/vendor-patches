<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Json;

use Migrify\VendorPatches\Exception\FileSystem\FileNotFoundException;
use Nette\Utils\Arrays;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;

final class JsonFileSystem
{
    public function loadFilePathToJson(string $filePath): array
    {
        if (! file_exists($filePath)) {
            throw new FileNotFoundException($filePath);
        }

        $fileContent = FileSystem::read($filePath);

        return Json::decode($fileContent, Json::FORCE_ARRAY);
    }

    public function writeJsonToFilePath(array $jsonArray, string $filePath): void
    {
        if (! file_exists($filePath)) {
            throw new FileNotFoundException($filePath);
        }

        $jsonContent = Json::encode($jsonArray, Json::PRETTY);

        FileSystem::write($filePath, $jsonContent);
    }

    public function mergeArrayToJsonFile(string $filePath, array $newJsonArray): void
    {
        $jsonArray = $this->loadFilePathToJson($filePath);

        $newComposerJsonArray = Arrays::mergeTree($jsonArray, $newJsonArray);

        $this->writeJsonToFilePath($newComposerJsonArray, $filePath);
    }
}
