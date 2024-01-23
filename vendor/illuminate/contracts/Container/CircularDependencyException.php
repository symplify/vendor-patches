<?php

namespace VendorPatches202401\Illuminate\Contracts\Container;

use Exception;
use VendorPatches202401\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
