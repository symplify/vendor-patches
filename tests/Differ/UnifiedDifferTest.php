<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\Differ;

use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\VendorPatches\Differ\UnifiedDiffer;
use Symplify\VendorPatches\Tests\AbstractTestCase;

final class UnifiedDifferTest extends AbstractTestCase
{
    private UnifiedDiffer $unifiedDiffer;

    protected function setUp(): void
    {
        $this->unifiedDiffer = $this->make(UnifiedDiffer::class);
    }

    #[DataProvider('provideData')]
    public function test(string $from, string $to, string $expectedDiff): void
    {
        $this->assertSame($expectedDiff, $this->unifiedDiffer->diff($from, $to));
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function provideData(): iterable
    {
        yield 'identical input has no hunks' => ["same\n", "same\n", "--- Original\n+++ New\n"];

        yield 'single line change collapses ranges' => [
            "before\n",
            "after\n",
            "--- Original\n+++ New\n@@ -1 +1 @@\n-before\n+after\n",
        ];

        yield 'change keeps surrounding context' => [
            "a\nb\nc\nd\ne\n",
            "a\nb\nX\nd\ne\n",
            "--- Original\n+++ New\n@@ -1,5 +1,5 @@\n a\n b\n-c\n+X\n d\n e\n",
        ];

        yield 'distant changes split into two hunks' => [
            "a\nb\nc\nd\ne\nf\ng\nh\n",
            "Z\nb\nc\nd\ne\nf\ng\nQ\n",
            "--- Original\n+++ New\n@@ -1,4 +1,4 @@\n-a\n+Z\n b\n c\n d\n@@ -5,4 +5,4 @@\n e\n f\n g\n-h\n+Q\n",
        ];

        yield 'inputs without trailing newline still diff cleanly' => [
            'before',
            'after',
            "--- Original\n+++ New\n@@ -1 +1 @@\n-before\n+after\n",
        ];
    }
}
