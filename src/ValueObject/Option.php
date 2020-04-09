<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\ValueObject;

final class Option
{
    /**
     * @var string
     */
    public const VENDOR_DIRECTORY = 'vendor_directory';

    /**
     * @var string
     */
    public const CHANGED_VENDOR_DIRECTORY = 'changed_vendor_directory';
}
