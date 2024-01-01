<?php

namespace VendorPatches202401\Illuminate\Contracts\Container;

use Exception;
use VendorPatches202401\Psr\Container\ContainerExceptionInterface;
class BindingResolutionException extends Exception implements ContainerExceptionInterface
{
    //
}
