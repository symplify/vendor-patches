<?php

// @see https://github.com/shipmonk-rnd/composer-dependency-analyser/
declare (strict_types=1);
namespace VendorPatches202607;

use VendorPatches202607\ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use VendorPatches202607\ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;
return (new Configuration())->ignoreErrorsOnExtension('ext-filter', [ErrorType::SHADOW_DEPENDENCY]);
