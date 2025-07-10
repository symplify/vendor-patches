<?php

namespace VendorPatches202507\Illuminate\Contracts\Container;

use Exception;
use VendorPatches202507\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
