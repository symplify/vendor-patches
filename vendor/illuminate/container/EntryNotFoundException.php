<?php

namespace VendorPatches202507\Illuminate\Container;

use Exception;
use VendorPatches202507\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
