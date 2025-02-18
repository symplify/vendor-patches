<?php

declare (strict_types=1);
namespace Symplify\VendorPatches\Differ;

use VendorPatches202502\Nette\Utils\Strings;
use VendorPatches202502\SebastianBergmann\Diff\Differ;
use Symplify\VendorPatches\Exception\ShouldNotHappenException;
use Symplify\VendorPatches\Utils\FileSystemHelper;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;
/**
 * @see \Symplify\VendorPatches\Tests\Differ\PatchDifferTest
 */
final class PatchDiffer
{
    /**
     * @readonly
     * @var \SebastianBergmann\Diff\Differ
     */
    private $differ;
    /**
     * @see https://regex101.com/r/0O5NO1/4
     * @var string
     */
    private const LOCAL_PATH_REGEX = '#vendor\\/[^\\/]+\\/[^\\/]+\\/(?<local_path>.*?)$#is';
    /**
     * @see https://regex101.com/r/vNa7PO/1
     * @var string
     */
    private const START_ORIGINAL_REGEX = '#^--- Original#';
    /**
     * @see https://regex101.com/r/o8C90E/1
     * @var string
     */
    private const START_NEW_REGEX = '#^\\+\\+\\+ New#m';
    public function __construct(Differ $differ)
    {
        $this->differ = $differ;
    }
    public function diff(OldAndNewFile $oldAndNewFile) : string
    {
        $diff = $this->differ->diff($oldAndNewFile->getOldFileContents(), $oldAndNewFile->getNewFileContents());
        $newFilePath = $oldAndNewFile->getNewFilePath();
        $patchedFileRelativePath = $this->resolveRelativeFilePath($newFilePath);
        $clearedDiff = Strings::replace($diff, self::START_ORIGINAL_REGEX, '--- /dev/null');
        return Strings::replace($clearedDiff, self::START_NEW_REGEX, '+++ ' . $patchedFileRelativePath);
    }
    private function resolveRelativeFilePath(string $beforeFilePath) : string
    {
        $match = Strings::match(FileSystemHelper::normalizePath($beforeFilePath), self::LOCAL_PATH_REGEX);
        if (!isset($match['local_path'])) {
            throw new ShouldNotHappenException();
        }
        return '../' . $match['local_path'];
    }
}
