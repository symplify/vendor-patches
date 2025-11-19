<?php

namespace VendorPatches202511\Illuminate\Contracts\Container;

use Exception;
use VendorPatches202511\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
