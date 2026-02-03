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
     * @see https://regex101.com/r/n2NXxy/2
     * @var string
     */
    private const LOCAL_PATH_REGEX = '#vendor/[^/]+/[^/]+/(?<local_path>.+)$#';

    public function __construct(
        private Differ $differ
    ) {
    }

    public function diff(OldAndNewFile $oldAndNewFile): string
    {
        $diff = $this->differ->diff($oldAndNewFile->getOldFileContents(), $oldAndNewFile->getNewFileContents());
        $patchedFileRelativePath = $this->resolveRelativeFilePath($oldAndNewFile->getNewFilePath());

        return "--- a/{$patchedFileRelativePath}\n+++ b/{$patchedFileRelativePath}\n{$diff}";
    }

    private function resolveRelativeFilePath(string $beforeFilePath): string
    {
        $match = Regex::match(FileSystemHelper::normalizePath($beforeFilePath), self::LOCAL_PATH_REGEX);

        if (! isset($match['local_path'])) {
            throw new ShouldNotHappenException();
        }

        return $match['local_path'];
    }
}
