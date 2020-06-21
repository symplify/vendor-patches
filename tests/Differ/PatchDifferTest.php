<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Tests\Differ;

use Migrify\VendorPatches\Differ\PatchDiffer;
use Migrify\VendorPatches\HttpKernel\VendorPatchesKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
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
        $beforeFileInfo = new SmartFileInfo(__DIR__ . '/PatchDifferSource/vendor/some/package/before_file.php');
        $afterFileInfo = new SmartFileInfo(__DIR__ . '/PatchDifferSource/vendor-changed/some/package/after_file.php');

        $diff = $this->patchDiffer->diff($beforeFileInfo, $afterFileInfo);
        $this->assertStringEqualsFile(__DIR__ . '/PatchDifferFixture/expected_diff.php', $diff);
    }
}
