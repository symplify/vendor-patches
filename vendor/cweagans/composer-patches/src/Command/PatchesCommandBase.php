<?php

declare (strict_types=1);
namespace VendorPatches202601\cweagans\Composer\Command;

use VendorPatches202601\Composer\Command\BaseCommand;
use VendorPatches202601\cweagans\Composer\Plugin\Patches;
abstract class PatchesCommandBase extends BaseCommand
{
    /**
     * Get the Patches plugin
     *
     * @return Patches|null
     */
    protected function getPatchesPluginInstance() : ?Patches
    {
        foreach ($this->requireComposer()->getPluginManager()->getPlugins() as $plugin) {
            if ($plugin instanceof Patches) {
                return $plugin;
            }
        }
        return null;
    }
}
