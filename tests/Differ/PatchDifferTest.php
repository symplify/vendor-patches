<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Tests\Differ;

use Migrify\VendorPatches\Differ\PatchDiffer;
use Migrify\VendorPatches\HttpKernel\VendorPatchesKernel;
use Migrify\VendorPatches\ValueObject\OldAndNewFileInfo;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PatchDifferTest extends AbstractKernelTestCase
{
    /**
     * @var PatchDiffer
     */
    private $patchDiffer;

    protected function setUp(): void
    {
        self::bootKernel(VendorPatchesKernel::class);

        $this->patchDiffer = self::$container->get(PatchDiffer::class);
    }

    public function test(): void
    {
        $oldFileInfo = new SmartFileInfo(__DIR__ . '/PatchDifferSource/vendor/some/package/file.php.old');
        $newFileInfo = new SmartFileInfo(__DIR__ . '/PatchDifferSource/vendor/some/package/file.php');

        $oldAndNewFileInfo = new OldAndNewFileInfo($oldFileInfo, $newFileInfo, 'some/package');

        $diff = $this->patchDiffer->diff($oldAndNewFileInfo);
        $this->assertStringEqualsFile(__DIR__ . '/PatchDifferFixture/expected_diff.php', $diff);
    }
}
