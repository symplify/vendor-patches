<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Differ;

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\DiffOutputBuilderInterface;
use SebastianBergmann\Diff\Output\StrictUnifiedDiffOutputBuilder;

final class PHPUnitDifferFactory
{
    public function create(): Differ
    {
        $strictUnifiedDiffOutputBuilder = $this->createDiffOutputBuilder();

        return new Differ($strictUnifiedDiffOutputBuilder);
    }

    private function createDiffOutputBuilder(): DiffOutputBuilderInterface
    {
        return new StrictUnifiedDiffOutputBuilder([
            'collapseRanges' => true,
            'contextLines' => 1,
            'commonLineThreshold' => 5,
            'fromFile' => '/dev/null',
            'toFile' => PatchDiffer::CHANGED_FILE_PLACEHOLDER,
        ]);
    }
}
