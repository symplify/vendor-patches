<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Differ;

use Entropy\Utils\Regex;
use SebastianBergmann\Diff\Differ;
use Symplify\VendorPatches\Exception\ShouldNotHappenException;
use Symplify\VendorPatches\Utils\FileSystemHelper;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;

/**
 * @see \Symplify\VendorPatches\Tests\Differ\PatchDifferTest
 */
final readonly class PatchDiffer
{
    /**
     * @see https://regex101.com/r/0O5NO1/4
     */
    private const string LOCAL_PATH_REGEX = '#vendor\/[^\/]+\/[^\/]+\/(?<local_path>.*?)$#is';

    /**
     * @see https://regex101.com/r/ARznJR/1
     */
    private const string END_NEW_REGEX = '#\.old$#m';

    /**
     * @see https://regex101.com/r/vNa7PO/1
     */
    private const string START_ORIGINAL_REGEX = '#^--- Original#';

    /**
     * @see https://regex101.com/r/o8C90E/1
     */
    private const string START_NEW_REGEX = '#^\+\+\+ New#m';

    public function __construct(
        private Differ $differ
    ) {
    }

    public function diff(OldAndNewFile $oldAndNewFile): string
    {
        $diff = $this->differ->diff($oldAndNewFile->getOldFileContents(), $oldAndNewFile->getNewFileContents());

        $oldFilePath = Regex::replace($oldAndNewFile->getOldFilePath(), self::END_NEW_REGEX, '');
        $patchedOldFileRelativePath = $this->resolveRelativeFilePath($oldFilePath);

        $newFilePath = $oldAndNewFile->getNewFilePath();
        $patchedNewFileRelativePath = $this->resolveRelativeFilePath($newFilePath);

        $clearedDiff = Regex::replace($diff, self::START_ORIGINAL_REGEX, '--- ' . $patchedOldFileRelativePath);

        return Regex::replace($clearedDiff, self::START_NEW_REGEX, '+++ ' . $patchedNewFileRelativePath);
    }

    private function resolveRelativeFilePath(string $beforeFilePath): string
    {
        $match = Regex::match(FileSystemHelper::normalizePath($beforeFilePath), self::LOCAL_PATH_REGEX);

        if (! isset($match['local_path'])) {
            throw new ShouldNotHappenException();
        }

        return '../' . $match['local_path'];
    }
}
