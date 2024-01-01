<?php

namespace VendorPatches202401\Illuminate\Container;

use Exception;
use VendorPatches202401\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
