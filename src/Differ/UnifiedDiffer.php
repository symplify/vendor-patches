<?php

declare (strict_types=1);
namespace Symplify\VendorPatches\Differ;

use SplFixedArray;
/**
 * Minimal unified-diff generator, replacement for sebastian/diff.
 * Produces a line-numbered unified diff with 3 context lines and collapsed single-line ranges.
 *
 * @see \Symplify\VendorPatches\Tests\Differ\UnifiedDifferTest
 */
final class UnifiedDiffer
{
    /**
     * @var int
     */
    private const OLD = 0;
    /**
     * @var int
     */
    private const ADDED = 1;
    /**
     * @var int
     */
    private const REMOVED = 2;
    /**
     * @var int
     */
    private const NO_LINE_END_EOF_WARNING = 4;
    /**
     * @var int
     */
    private const CONTEXT_LINES = 3;
    /**
     * @var int
     */
    private const COMMON_LINE_THRESHOLD = 6;
    public function diff(string $from, string $to) : string
    {
        $diff = $this->diffToArray($this->splitByLines($from), $this->splitByLines($to));
        return $this->render($diff);
    }
    /**
     * @param string[] $from
     * @param string[] $to
     * @return list<array{string, int}>
     */
    private function diffToArray(array $from, array $to) : array
    {
        $fromLength = \count($from);
        $toLength = \count($to);
        $minLength = \min($fromLength, $toLength);
        // trim common prefix and suffix so the LCS runs on the changed middle only,
        // this also keeps the alignment identical for lines that repeat
        $prefix = 0;
        while ($prefix < $minLength && $from[$prefix] === $to[$prefix]) {
            ++$prefix;
        }
        $suffix = 0;
        while ($suffix < $minLength - $prefix && $from[$fromLength - 1 - $suffix] === $to[$toLength - 1 - $suffix]) {
            ++$suffix;
        }
        $middleFrom = \array_slice($from, $prefix, $fromLength - $prefix - $suffix);
        $middleTo = \array_slice($to, $prefix, $toLength - $prefix - $suffix);
        $diff = [];
        foreach (\array_slice($from, 0, $prefix) as $token) {
            $diff[] = [$token, self::OLD];
        }
        $common = $this->longestCommonSubsequence($middleFrom, $middleTo);
        $fromIndex = 0;
        $toIndex = 0;
        foreach ($common as $token) {
            while ($fromIndex < \count($middleFrom) && $middleFrom[$fromIndex] !== $token) {
                $diff[] = [$middleFrom[$fromIndex], self::REMOVED];
                ++$fromIndex;
            }
            while ($toIndex < \count($middleTo) && $middleTo[$toIndex] !== $token) {
                $diff[] = [$middleTo[$toIndex], self::ADDED];
                ++$toIndex;
            }
            $diff[] = [$token, self::OLD];
            ++$fromIndex;
            ++$toIndex;
        }
        while ($fromIndex < \count($middleFrom)) {
            $diff[] = [$middleFrom[$fromIndex], self::REMOVED];
            ++$fromIndex;
        }
        while ($toIndex < \count($middleTo)) {
            $diff[] = [$middleTo[$toIndex], self::ADDED];
            ++$toIndex;
        }
        foreach (\array_slice($from, $fromLength - $suffix) as $token) {
            $diff[] = [$token, self::OLD];
        }
        return $diff;
    }
    /**
     * @return string[]
     */
    private function splitByLines(string $input) : array
    {
        $lines = \preg_split('/(.*\\R)/', $input, -1, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY);
        return $lines === \false ? [] : $lines;
    }
    /**
     * @param string[] $from
     * @param string[] $to
     * @return string[]
     */
    private function longestCommonSubsequence(array $from, array $to) : array
    {
        $fromLength = \count($from);
        $toLength = \count($to);
        $width = $fromLength + 1;
        $matrix = new SplFixedArray($width * ($toLength + 1));
        for ($i = 0; $i <= $fromLength; ++$i) {
            $matrix[$i] = 0;
        }
        for ($j = 0; $j <= $toLength; ++$j) {
            $matrix[$j * $width] = 0;
        }
        for ($i = 1; $i <= $fromLength; ++$i) {
            for ($j = 1; $j <= $toLength; ++$j) {
                $offset = $j * $width + $i;
                $diagonal = $from[$i - 1] === $to[$j - 1] ? $matrix[$offset - $width - 1] + 1 : 0;
                $matrix[$offset] = \max($diagonal, $matrix[$offset - 1], $matrix[$offset - $width]);
            }
        }
        $common = [];
        $i = $fromLength;
        $j = $toLength;
        while ($i > 0 && $j > 0) {
            if ($from[$i - 1] === $to[$j - 1]) {
                $common[] = $from[$i - 1];
                --$i;
                --$j;
            } elseif ($matrix[$j * $width + $i - $width] > $matrix[$j * $width + $i - 1]) {
                --$j;
            } else {
                --$i;
            }
        }
        return \array_reverse($common);
    }
    /**
     * @param list<array{string, int}> $diff
     */
    private function render(array $diff) : string
    {
        $output = "--- Original\n+++ New\n";
        if ($diff === []) {
            return $output;
        }
        $diff = $this->markMissingEndOfFileNewline($diff);
        $output .= $this->renderHunks($diff);
        $last = \substr($output, -1);
        if ($last !== "\n" && $last !== "\r") {
            $output .= "\n";
        }
        return $output;
    }
    /**
     * @param list<array{string, int}> $diff
     * @return list<array{string, int}>
     */
    private function markMissingEndOfFileNewline(array $diff) : array
    {
        $warning = ["\n\\ No newline at end of file\n", self::NO_LINE_END_EOF_WARNING];
        $lastIndex = \count($diff) - 1;
        if ($diff[$lastIndex][1] === self::OLD) {
            if (\substr_compare($diff[$lastIndex][0], "\n", -\strlen("\n")) !== 0) {
                \array_splice($diff, $lastIndex + 1, 0, [$warning]);
            }
            return $diff;
        }
        // find the last added and removed line, warn under each without a trailing newline
        $toFind = [self::ADDED => \true, self::REMOVED => \true];
        for ($i = $lastIndex; $i >= 0; --$i) {
            $type = $diff[$i][1];
            if (!isset($toFind[$type])) {
                continue;
            }
            unset($toFind[$type]);
            if (\substr_compare($diff[$i][0], "\n", -\strlen("\n")) !== 0) {
                \array_splice($diff, $i + 1, 0, [$warning]);
            }
            if ($toFind === []) {
                break;
            }
        }
        return $diff;
    }
    /**
     * Groups changed lines into hunks with surrounding context, flushing a hunk once
     * a run of unchanged lines reaches the cutoff.
     *
     * @param list<array{string, int}> $diff
     */
    private function renderHunks(array $diff) : string
    {
        $output = '';
        $cutOff = \max(self::COMMON_LINE_THRESHOLD, self::CONTEXT_LINES);
        $hunkCapture = \false;
        $sameCount = 0;
        $toRange = 0;
        $fromRange = 0;
        $toStart = 1;
        $fromStart = 1;
        $i = 0;
        foreach ($diff as $i => $entry) {
            if ($entry[1] === self::OLD) {
                if ($hunkCapture === \false) {
                    ++$fromStart;
                    ++$toStart;
                    continue;
                }
                ++$sameCount;
                ++$toRange;
                ++$fromRange;
                if ($sameCount === $cutOff) {
                    $contextStartOffset = $hunkCapture - self::CONTEXT_LINES < 0 ? $hunkCapture : self::CONTEXT_LINES;
                    $output .= $this->renderHunk($diff, $hunkCapture - $contextStartOffset, $i - $cutOff + self::CONTEXT_LINES + 1, $fromStart - $contextStartOffset, $fromRange - $cutOff + $contextStartOffset + self::CONTEXT_LINES, $toStart - $contextStartOffset, $toRange - $cutOff + $contextStartOffset + self::CONTEXT_LINES);
                    $fromStart += $fromRange;
                    $toStart += $toRange;
                    $hunkCapture = \false;
                    $sameCount = 0;
                    $toRange = 0;
                    $fromRange = 0;
                }
                continue;
            }
            $sameCount = 0;
            if ($entry[1] === self::NO_LINE_END_EOF_WARNING) {
                continue;
            }
            if ($hunkCapture === \false) {
                $hunkCapture = $i;
            }
            if ($entry[1] === self::ADDED) {
                ++$toRange;
            }
            if ($entry[1] === self::REMOVED) {
                ++$fromRange;
            }
        }
        if ($hunkCapture === \false) {
            return $output;
        }
        $contextStartOffset = $hunkCapture - self::CONTEXT_LINES < 0 ? $hunkCapture : self::CONTEXT_LINES;
        $contextEndOffset = \min($sameCount, self::CONTEXT_LINES);
        $fromRange -= $sameCount;
        $toRange -= $sameCount;
        return $output . $this->renderHunk($diff, $hunkCapture - $contextStartOffset, $i - $sameCount + $contextEndOffset + 1, $fromStart - $contextStartOffset, $fromRange + $contextStartOffset + $contextEndOffset, $toStart - $contextStartOffset, $toRange + $contextStartOffset + $contextEndOffset);
    }
    /**
     * @param list<array{string, int}> $diff
     */
    private function renderHunk(array $diff, int $diffStartIndex, int $diffEndIndex, int $fromStart, int $fromRange, int $toStart, int $toRange) : string
    {
        $output = $this->renderHunkHeader($fromStart, $fromRange, $toStart, $toRange);
        for ($i = $diffStartIndex; $i < $diffEndIndex; ++$i) {
            [$line, $type] = $diff[$i];
            if ($type === self::ADDED) {
                $output .= '+' . $line;
            } elseif ($type === self::REMOVED) {
                $output .= '-' . $line;
            } elseif ($type === self::NO_LINE_END_EOF_WARNING) {
                $output .= "\n";
            } else {
                $output .= ' ' . $line;
            }
        }
        return $output;
    }
    private function renderHunkHeader(int $fromStart, int $fromRange, int $toStart, int $toRange) : string
    {
        $from = $fromRange === 1 ? (string) $fromStart : $fromStart . ',' . $fromRange;
        $to = $toRange === 1 ? (string) $toStart : $toStart . ',' . $toRange;
        return '@@ -' . $from . ' +' . $to . " @@\n";
    }
}
