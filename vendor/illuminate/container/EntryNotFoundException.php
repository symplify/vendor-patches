<?php

namespace VendorPatches202502\Illuminate\Container;

use Exception;
use VendorPatches202502\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
