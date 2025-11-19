<?php

namespace VendorPatches202511\Illuminate\Container;

use Exception;
use VendorPatches202511\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
