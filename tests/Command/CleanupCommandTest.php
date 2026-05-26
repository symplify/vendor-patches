<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Symplify\VendorPatches\Command\CleanupCommand;
use Symplify\VendorPatches\Tests\AbstractTestCase;

final class CleanupCommandTest extends AbstractTestCase
{
    private string $workDirectory;

    private string $originalCwd;

    protected function setUp(): void
    {
        $this->workDirectory = sys_get_temp_dir() . '/vendor-patches-cleanup-' . uniqid();
        mkdir($this->workDirectory . '/vendor/some/package/src', 0777, true);

        $this->originalCwd = (string) getcwd();
        chdir($this->workDirectory);
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
        $this->removeRecursive($this->workDirectory);
    }

    public function testRemovesOldFiles(): void
    {
        $vendorDir = $this->workDirectory . '/vendor';
        $oldFileA = $vendorDir . '/some/package/src/A.php.old';
        $oldFileB = $vendorDir . '/some/package/src/B.php.old';
        $kept = $vendorDir . '/some/package/src/A.php';

        file_put_contents($oldFileA, '<?php // old A');
        file_put_contents($oldFileB, '<?php // old B');
        file_put_contents($kept, '<?php // current');

        $cleanupCommand = $this->make(CleanupCommand::class);
        $exitCode = $cleanupCommand->run();

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
        $this->assertFileDoesNotExist($oldFileA);
        $this->assertFileDoesNotExist($oldFileB);
        $this->assertFileExists($kept);
    }

    public function testReturnsSuccessWhenNothingToRemove(): void
    {
        $cleanupCommand = $this->make(CleanupCommand::class);
        $exitCode = $cleanupCommand->run();

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
    }

    private function removeRecursive(string $path): void
    {
        if (! file_exists($path)) {
            return;
        }

        if (is_file($path) || is_link($path)) {
            unlink($path);
            return;
        }

        foreach (scandir($path) ?: [] as $entry) {
            if ($entry === '.') {
                continue;
            }
            if ($entry === '..') {
                continue;
            }

            $this->removeRecursive($path . '/' . $entry);
        }

        rmdir($path);
    }
}
