<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Differ;

use Migrify\VendorPatches\Exception\ShouldNotHappenException;
use Nette\Utils\Strings;
use SebastianBergmann\Diff\Differ;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PatchDiffer
{
    /**
     * @see https://regex101.com/r/0O5NO1/1/
     * @var string
     */
    private const LOCAL_PATH_PATTERN = '#vendor\/(\w|\-)+\/(\w|\-)+\/(?<local_path>.*?)$#is';

    /**
     * @var Differ
     */
    private $differ;

    public function __construct(Differ $differ)
    {
        $this->differ = $differ;
    }

    public function diff(SmartFileInfo $beforeFileInfo, SmartFileInfo $afterFileInfo): string
    {
        $diff = $this->differ->diff($beforeFileInfo->getContents(), $afterFileInfo->getContents());

        $patchedFileRelativePath = $this->resolveFileInfoPathRelativeFilePath($beforeFileInfo);

        $diff = Strings::replace($diff, '#^--- Original#', '--- /dev/null');
        return Strings::replace($diff, '#^\+\+\+ New#m', '+++ ' . $patchedFileRelativePath);
    }

    private function resolveFileInfoPathRelativeFilePath(SmartFileInfo $beforeFileInfo): string
    {
        $match = Strings::match($beforeFileInfo->getRealPath(), self::LOCAL_PATH_PATTERN);
        if (! isset($match['local_path'])) {
            throw new ShouldNotHappenException();
        }

        return '../' . $match['local_path'];
    }
}
