<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\Composer\ComposerPatchesConfigurationUpdater;

use Symplify\VendorPatches\Composer\ComposerPatchesConfigurationUpdater;
use Symplify\VendorPatches\Tests\AbstractTestCase;

final class ComposerPatchesConfigurationUpdaterTest extends AbstractTestCase
{
    public function testComposerJson(): void
    {
        $composerPatchesConfigurationUpdater = $this->make(ComposerPatchesConfigurationUpdater::class);

        $composerJson = $composerPatchesConfigurationUpdater->updateComposerJson(
            __DIR__ . '/Fixture/composer.already_has_patches.json',
            [
                'some_package' => ['some.patch'],
            ]
        );

        $this->assertSame([
            'patches' => [
                'some_package' => ['some.patch'],
                'symfony/console' => ['patches/symfony-console-style-symfonystyle-php.patch'],
            ],
        ], $composerJson['extra']);
    }

    public function testPatchesFile(): void
    {
        $composerPatchesConfigurationUpdater = $this->make(ComposerPatchesConfigurationUpdater::class);

        $patchesFileJson = $composerPatchesConfigurationUpdater->updatePatchesFileJson(
            __DIR__ . '/Fixture/patches_file.already_has_patches.json',
            [
                'some_package' => ['some.patch'],
            ]
        );

        $this->assertSame([
            'patches' => [
                'some_package' => ['some.patch'],
                'symfony/console' => ['patches/symfony-console-style-symfonystyle-php.patch'],
            ],
        ], $patchesFileJson);
    }

    public function testComposerJsonAndPrint(): void
    {
        $composerPatchesConfigurationUpdater = $this->make(ComposerPatchesConfigurationUpdater::class);

        // no changes expected
        $composerExtraPatches = [];
        $composerJsonFilePath = __DIR__ . '/Fixture/composer.already_has_patches.json';

        // prepare temp file
        $tempFilePath = tempnam(sys_get_temp_dir(), 'symplify_vendor_patches_composer_json_');
        if ($tempFilePath === false) {
            $this->markTestSkipped('Could not create temporary file.');
        }

        copy($composerJsonFilePath, $tempFilePath);
        if (! copy($composerJsonFilePath, $tempFilePath)) {
            unlink($tempFilePath);
            $this->markTestSkipped('Failed to copy fixture to temporary file');
        }

        try {
            $composerPatchesConfigurationUpdater->updateComposerJsonAndPrint($tempFilePath, $composerExtraPatches);

            $this->assertFileEquals($composerJsonFilePath, $tempFilePath);
        } finally {
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
        }
    }
}
