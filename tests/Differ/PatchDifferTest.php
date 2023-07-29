<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\Differ;

use Symplify\VendorPatches\Differ\PatchDiffer;
use Symplify\VendorPatches\Tests\AbstractTestCase;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;

final class PatchDifferTest extends AbstractTestCase
{
    public function test(): void
    {
        $patchDiffer = $this->make(PatchDiffer::class);

        $oldFilePath = __DIR__ . '/PatchDifferSource/vendor/some/package/file.php.old';
        $newFilePath = __DIR__ . '/PatchDifferSource/vendor/some/package/file.php';

        $oldAndNewFile = new OldAndNewFile($oldFilePath, $newFilePath, 'some/package');

        $diff = $patchDiffer->diff($oldAndNewFile);
        $this->assertStringEqualsFile(__DIR__ . '/PatchDifferFixture/expected_diff.php', $diff);
    }
}
