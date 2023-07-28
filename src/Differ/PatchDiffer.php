<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Differ;

use Nette\Utils\Strings;
use SebastianBergmann\Diff\Differ;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;

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
        private readonly Differ $differ
    ) {
    }

    public function diff(OldAndNewFile $oldAndNewFile): string
    {
        $oldFileInfo = $oldAndNewFile->getOldFilePath();
        $newFileInfo = $oldAndNewFile->getNewFilePath();

        $diff = $this->differ->diff($oldAndNewFile->getOldFileContents(), $oldAndNewFile->getNewFileContents());

        $patchedFileRelativePath = $this->resolveFileInfoPathRelativeFilePath($newFileInfo);

        $clearedDiff = Strings::replace($diff, self::START_ORIGINAL_REGEX, '--- /dev/null');
        return Strings::replace($clearedDiff, self::START_NEW_REGEX, '+++ ' . $patchedFileRelativePath);
    }

    private function resolveFileInfoPathRelativeFilePath(string $beforeFilePath): string
    {
        $match = Strings::match($beforeFilePath, self::LOCAL_PATH_REGEX);

        if (! isset($match['local_path'])) {
            throw new \Symplify\VendorPatches\Exception\ShouldNotHappenException();
        }

        return '../' . $match['local_path'];
    }
}
