<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\Composer\ComposerPatchesConfigurationUpdater;

use Symplify\VendorPatches\Composer\ComposerPatchesConfigurationUpdater;
use Symplify\VendorPatches\Tests\AbstractTestCase;

final class ComposerPatchesConfigurationUpdaterTest extends AbstractTestCase
{
    private ComposerPatchesConfigurationUpdater $composerPatchesConfigurationUpdater;

    protected function setUp(): void
    {
        parent::setUp();

        $this->composerPatchesConfigurationUpdater = $this->make(ComposerPatchesConfigurationUpdater::class);
    }

    public function test(): void
    {
        $composerJson = $this->composerPatchesConfigurationUpdater->updateComposerJson(
            __DIR__ . '/Fixture/already_has_patches.json',
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
}
