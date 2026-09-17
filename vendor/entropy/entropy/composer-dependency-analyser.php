<?php

// @see https://github.com/shipmonk-rnd/composer-dependency-analyser/
declare (strict_types=1);
namespace VendorPatches202609;

use VendorPatches202609\ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use VendorPatches202609\ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;
return (new Configuration())->ignoreErrorsOnExtension('ext-filter', [ErrorType::SHADOW_DEPENDENCY]);
