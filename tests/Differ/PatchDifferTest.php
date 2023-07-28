<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\Differ;

use Symplify\VendorPatches\Differ\PatchDiffer;
use Symplify\VendorPatches\Tests\AbstractTestCase;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;

final class PatchDifferTest extends AbstractTestCase
{
    private PatchDiffer $patchDiffer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->patchDiffer = $this->make(PatchDiffer::class);
    }

    public function test(): void
    {
        $oldFilePath = __DIR__ . '/PatchDifferSource/vendor/some/package/file.php.old';
        $newFilePath = __DIR__ . '/PatchDifferSource/vendor/some/package/file.php';

        $oldAndNewFileInfo = new OldAndNewFile($oldFilePath, $newFilePath, 'some/package');

        $diff = $this->patchDiffer->diff($oldAndNewFileInfo);
        $this->assertStringEqualsFile(__DIR__ . '/PatchDifferFixture/expected_diff.php', $diff);
    }
}
