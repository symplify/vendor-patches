<?php

declare (strict_types=1);
namespace VendorPatches20220610\Symplify\PackageBuilder\Console\Input;

use VendorPatches20220610\Symfony\Component\Console\Input\ArgvInput;
/**
 * @api
 */
final class StaticInputDetector
{
    public static function isDebug() : bool
    {
        $argvInput = new ArgvInput();
        return $argvInput->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
    }
}
