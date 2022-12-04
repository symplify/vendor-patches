<?php

declare (strict_types=1);
namespace VendorPatches202212\Symplify\PackageBuilder\Console\Input;

use VendorPatches202212\Symfony\Component\Console\Input\ArgvInput;
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
