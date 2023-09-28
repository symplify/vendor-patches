<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\PatchFileFactory;

use Symplify\VendorPatches\PatchFileFactory;
use Symplify\VendorPatches\Tests\AbstractTestCase;
use Symplify\VendorPatches\Utils\FileSystemHelper;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;

final class PatchFileFactoryTest extends AbstractTestCase
{
    public function test(): void
    {
        $patchFileFactory = $this->make(PatchFileFactory::class);

        $oldAndNewFile = new OldAndNewFile(
            __DIR__ . '/Fixture/some_old_file.php',
            __DIR__ . '/Fixture/some_new_file.php',
            'package/name'
        );

        $pathFilePath = $patchFileFactory->createPatchFilePath($oldAndNewFile, __DIR__ . '/Fixture');
        $this->assertSame('patches/some-new-file-php.patch', FileSystemHelper::normalizePath($pathFilePath));
    }
}
