<?php

declare (strict_types=1);
namespace VendorPatches202302;

use VendorPatches202302\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use VendorPatches202302\Symplify\EasyCI\Config\EasyCIConfig;
return static function (EasyCIConfig $easyCIConfig) : void {
    $easyCIConfig->typesToSkip([AutowireArrayParameterCompilerPass::class]);
};
