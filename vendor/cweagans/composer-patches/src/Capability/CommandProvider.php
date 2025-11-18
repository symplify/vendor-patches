<?php

declare (strict_types=1);
namespace VendorPatches202511\cweagans\Composer\Capability;

use VendorPatches202511\Composer\Plugin\Capability\CommandProvider as CommandProviderInterface;
use VendorPatches202511\cweagans\Composer\Command\DoctorCommand;
use VendorPatches202511\cweagans\Composer\Command\RepatchCommand;
use VendorPatches202511\cweagans\Composer\Command\RelockCommand;
class CommandProvider implements CommandProviderInterface
{
    public function getCommands() : array
    {
        return [new DoctorCommand(), new RepatchCommand(), new RelockCommand()];
    }
}
