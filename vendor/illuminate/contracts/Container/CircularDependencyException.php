<?php

namespace VendorPatches202502\Illuminate\Contracts\Container;

use Exception;
use VendorPatches202502\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
