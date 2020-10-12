<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Tests\Json;

use Migrify\VendorPatches\HttpKernel\VendorPatchesKernel;
use Migrify\VendorPatches\Json\JsonFileSystem;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileSystem;

final class JsonFileSystemTest extends AbstractKernelTestCase
{
    /**
     * @var JsonFileSystem
     */
    private $jsonFileSystem;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    protected function setUp(): void
    {
        self::bootKernel(VendorPatchesKernel::class);
        $this->jsonFileSystem = self::$container->get(JsonFileSystem::class);
        $this->smartFileSystem = self::$container->get(SmartFileSystem::class);
    }

    public function testLoadFilePathToJson(): void
    {
        $json = $this->jsonFileSystem->loadFilePathToJson(__DIR__ . '/JsonFileSystemSource/some_file.json');
        $this->assertSame([
            'key' => 'value',
        ], $json);
    }

    public function testWriteJsonToFilePath(): void
    {
        $filePath = __DIR__ . '/JsonFileSystemSource/temp_file.json';

        $this->jsonFileSystem->writeJsonToFilePath([
            'hey' => 'you',
        ], $filePath);
        $this->assertFileExists($filePath);

        $expectedFilePath = __DIR__ . '/JsonFileSystemSource/expected_temp_file.json';
        $this->assertFileEquals($expectedFilePath, $filePath);

        $this->smartFileSystem->remove($filePath);
    }

    public function testMergeArrayToJsonFile(): void
    {
        $originalFilePath = __DIR__ . '/JsonFileSystemSource/original.json';
        $temporaryFilePath = $originalFilePath . '-copy';

        $this->smartFileSystem->copy($originalFilePath, $temporaryFilePath);

        $this->jsonFileSystem->mergeArrayToJsonFile($temporaryFilePath, [
            'new' => 'data',
        ]);

        $expectedFilePath = __DIR__ . '/JsonFileSystemSource/expected_merged_original.json';
        $this->assertFileEquals($expectedFilePath, $temporaryFilePath);

        $this->smartFileSystem->remove($temporaryFilePath);
    }
}
