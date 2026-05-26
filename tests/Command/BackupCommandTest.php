<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Symplify\VendorPatches\Command\BackupCommand;
use Symplify\VendorPatches\Tests\AbstractTestCase;

final class BackupCommandTest extends AbstractTestCase
{
    private string $workDirectory;

    protected function setUp(): void
    {
        $this->workDirectory = sys_get_temp_dir() . '/vendor-patches-backup-' . uniqid();
        mkdir($this->workDirectory, 0777, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->workDirectory . '/*') ?: [] as $file) {
            unlink($file);
        }
        rmdir($this->workDirectory);
    }

    public function testCreatesOldBackupFile(): void
    {
        $sourceFile = $this->workDirectory . '/Sample.php';
        file_put_contents($sourceFile, "<?php\n// sample\n");

        $backupCommand = $this->make(BackupCommand::class);
        $exitCode = $backupCommand->run($sourceFile);

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
        $this->assertFileExists($sourceFile . '.old');
        $this->assertSame(file_get_contents($sourceFile), file_get_contents($sourceFile . '.old'));
    }

    public function testBacksUpMultipleFiles(): void
    {
        $first = $this->workDirectory . '/First.php';
        $second = $this->workDirectory . '/Second.php';
        file_put_contents($first, '<?php // first');
        file_put_contents($second, '<?php // second');

        $backupCommand = $this->make(BackupCommand::class);
        $exitCode = $backupCommand->run($first, $second);

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
        $this->assertFileExists($first . '.old');
        $this->assertFileExists($second . '.old');
    }

    public function testReturnsErrorWhenNoFilesProvided(): void
    {
        $backupCommand = $this->make(BackupCommand::class);
        $exitCode = $backupCommand->run();

        $this->assertSame(ExitCode::ERROR, $exitCode);
    }

    public function testReturnsErrorWhenFileMissing(): void
    {
        $backupCommand = $this->make(BackupCommand::class);
        $exitCode = $backupCommand->run($this->workDirectory . '/does-not-exist.php');

        $this->assertSame(ExitCode::ERROR, $exitCode);
    }

    public function testDoesNotOverwriteExistingBackup(): void
    {
        $sourceFile = $this->workDirectory . '/Sample.php';
        file_put_contents($sourceFile, '<?php // new content');
        file_put_contents($sourceFile . '.old', '<?php // original');

        $backupCommand = $this->make(BackupCommand::class);
        $exitCode = $backupCommand->run($sourceFile);

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
        $this->assertSame('<?php // original', file_get_contents($sourceFile . '.old'));
    }
}
