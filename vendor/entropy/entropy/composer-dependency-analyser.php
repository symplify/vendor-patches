<?php

// @see https://github.com/shipmonk-rnd/composer-dependency-analyser/
declare (strict_types=1);
namespace VendorPatches202601;

use VendorPatches202601\ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use VendorPatches202601\ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;
return (new Configuration())->ignoreErrorsOnExtension('ext-filter', [ErrorType::SHADOW_DEPENDENCY]);
