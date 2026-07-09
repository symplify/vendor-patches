<?php

declare (strict_types=1);
namespace VendorPatches202607\cweagans\Composer\Command;

use VendorPatches202607\Composer\Command\BaseCommand;
use VendorPatches202607\cweagans\Composer\Plugin\Patches;
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
