<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Tests\Json;

use Migrify\VendorPatches\HttpKernel\VendorPatchesKernel;
use Migrify\VendorPatches\Json\JsonFileSystem;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class JsonFileSystemTest extends AbstractKernelTestCase
{
    /**
     * @var JsonFileSystem
     */
    private $jsonFileSystem;

    protected function setUp(): void
    {
        self::bootKernel(VendorPatchesKernel::class);
        $this->jsonFileSystem = self::$container->get(JsonFileSystem::class);
    }

    public function test(): void
    {
        $json = $this->jsonFileSystem->loadFilePathToJson(__DIR__ . '/JsonFileSystemSource/some_file.json');
        $this->assertSame(['key' => 'value'], $json);
    }
}
