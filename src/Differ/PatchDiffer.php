<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Differ;

use Migrify\MigrifyKernel\Exception\ShouldNotHappenException;
use Migrify\VendorPatches\ValueObject\OldAndNewFileInfo;
use Nette\Utils\Strings;
use SebastianBergmann\Diff\Differ;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Migrify\VendorPatches\Tests\Differ\PatchDifferTest
 */
final class PatchDiffer
{
    /**
     * @see https://regex101.com/r/0O5NO1/1/
     * @var string
     */
    private const LOCAL_PATH_REGEX = '#vendor\/(\w|\-)+\/(\w|\-)+\/(?<local_path>.*?)$#is';

    /**
     * @var Differ
     */
    private $differ;

    public function __construct(Differ $differ)
    {
        $this->differ = $differ;
    }

    public function diff(OldAndNewFileInfo $oldAndNewFileInfo): string
    {
        $oldFileInfo = $oldAndNewFileInfo->getOldFileInfo();
        $newFileInfo = $oldAndNewFileInfo->getNewFileInfo();

        $diff = $this->differ->diff($oldFileInfo->getContents(), $newFileInfo->getContents());

        $patchedFileRelativePath = $this->resolveFileInfoPathRelativeFilePath($newFileInfo);

        $diff = Strings::replace($diff, '#^--- Original#', '--- /dev/null');
        return Strings::replace($diff, '#^\+\+\+ New#m', '+++ ' . $patchedFileRelativePath);
    }

    private function resolveFileInfoPathRelativeFilePath(SmartFileInfo $beforeFileInfo): string
    {
        $match = Strings::match($beforeFileInfo->getRealPath(), self::LOCAL_PATH_REGEX);
        if (! isset($match['local_path'])) {
            throw new ShouldNotHappenException();
        }

        return '../' . $match['local_path'];
    }
}
