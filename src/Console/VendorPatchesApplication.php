<?php

declare (strict_types=1);
namespace Symplify\VendorPatches\Console;

use VendorPatches20220612\Symfony\Component\Console\Application;
use VendorPatches20220612\Symfony\Component\Console\Command\Command;
final class VendorPatchesApplication extends Application
{
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $this->addCommands($commands);
        parent::__construct();
    }
}
