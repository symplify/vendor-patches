<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\PatchFileFactory;

use Symplify\VendorPatches\PatchFileFactory;
use Symplify\VendorPatches\Tests\AbstractTestCase;
use Symplify\VendorPatches\ValueObject\OldAndNewFile;

final class PatchFileFactoryTest extends AbstractTestCase
{
    private const FIXTURE_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'Fixture';

    private const NESTED_OUTPUT_PATH = 'path' . DIRECTORY_SEPARATOR . 'to' . DIRECTORY_SEPARATOR . 'patches';

    public function testDefaultOutputPath(): void
    {
        $patchFilePath = $this->makePatchFilePath();
        $expectedPath = PatchFileFactory::DEFAULT_OUTPUT_PATH . DIRECTORY_SEPARATOR . 'some-new-file-php.patch';

        $this->assertSame($expectedPath, $patchFilePath);
    }

    public function testRelativeEnvironmentOutputPath(): void
    {
        $relativeOutputPath = self::NESTED_OUTPUT_PATH;
        $patchFilePath = $this->makePatchFilePathWithEnvironmentOutputPath($relativeOutputPath);
        $expectedPath = self::NESTED_OUTPUT_PATH . DIRECTORY_SEPARATOR . 'some-new-file-php.patch';

        $this->assertSame($expectedPath, $patchFilePath);
    }

    public function testAbsoluteEnvironmentOutputPath(): void
    {
        $absoluteOutputPath = dirname(__FILE__, 3) . DIRECTORY_SEPARATOR . self::NESTED_OUTPUT_PATH;
        $patchFilePath = $this->makePatchFilePathWithEnvironmentOutputPath($absoluteOutputPath);
        $expectedPath = self::NESTED_OUTPUT_PATH . DIRECTORY_SEPARATOR . 'some-new-file-php.patch';

        $this->assertSame($expectedPath, $patchFilePath);
    }

    private function makePatchFilePath(): string
    {
        $patchFileFactory = $this->make(PatchFileFactory::class);

        $oldAndNewFile = new OldAndNewFile(
            self::FIXTURE_PATH . DIRECTORY_SEPARATOR . 'some_old_file.php',
            self::FIXTURE_PATH . DIRECTORY_SEPARATOR . 'some_new_file.php',
            'package/name'
        );

        return $patchFileFactory->createPatchFilePath($oldAndNewFile, self::FIXTURE_PATH);
    }

    private function makePatchFilePathWithEnvironmentOutputPath(string $environmentOutputPath): string
    {
        putenv(PatchFileFactory::OUTPUT_PATH_ENV_VAR . '=' . $environmentOutputPath);

        $patchFilePath = $this->makePatchFilePath();

        putenv(PatchFileFactory::OUTPUT_PATH_ENV_VAR); // Unset

        return $patchFilePath;
    }
}
