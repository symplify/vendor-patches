<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Differ;

use Nette\Utils\Strings;
use SebastianBergmann\Diff\Differ;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use Symplify\VendorPatches\ValueObject\OldAndNewFileInfo;

/**
 * @see \Symplify\VendorPatches\Tests\Differ\PatchDifferTest
 */
final class PatchDiffer
{
    /**
     * @see https://regex101.com/r/0O5NO1/4
     * @var string
     */
    private const LOCAL_PATH_REGEX = '#vendor\/[^\/]+\/[^\/]+\/(?<local_path>.*?)$#is';

    /**
     * @see https://regex101.com/r/vNa7PO/1
     * @var string
     */
    private const START_ORIGINAL_REGEX = '#^--- Original#';

    /**
     * @see https://regex101.com/r/o8C90E/1
     * @var string
     */
    private const START_NEW_REGEX = '#^\+\+\+ New#m';

    public function __construct(
        private Differ $differ
    ) {
    }

    public function diff(OldAndNewFileInfo $oldAndNewFileInfo): string
    {
        $oldFileInfo = $oldAndNewFileInfo->getOldFileInfo();
        $newFileInfo = $oldAndNewFileInfo->getNewFileInfo();

        $diff = $this->differ->diff($oldFileInfo->getContents(), $newFileInfo->getContents());

        $patchedFileRelativePath = $this->resolveFileInfoPathRelativeFilePath($newFileInfo);

        $clearedDiff = Strings::replace($diff, self::START_ORIGINAL_REGEX, '--- /dev/null');
        return Strings::replace($clearedDiff, self::START_NEW_REGEX, '+++ ' . $patchedFileRelativePath);
    }

    private function resolveFileInfoPathRelativeFilePath(SmartFileInfo $beforeFileInfo): string
    {
        $match = Strings::match(str_replace('\\', '/', $beforeFileInfo->getRealPath()), self::LOCAL_PATH_REGEX);
        if (! isset($match['local_path'])) {
            throw new ShouldNotHappenException();
        }

        return '../' . $match['local_path'];
    }
}
