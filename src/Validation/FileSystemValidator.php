<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\Validation;

use Migrify\VendorPatches\Exception\InvalidDirectoryException;

final class FileSystemValidator
{
    public function ensureIsDirectoryAndDirectoryExists(string $directory): void
    {
        if (! file_exists($directory)) {
            $message = sprintf('Directory "%s" is not directory' . PHP_EOL, $directory);

            throw new InvalidDirectoryException($message);
        }

        if (! is_dir($directory)) {
            $message = sprintf('Directory "%s" was not found' . PHP_EOL, $directory);

            throw new InvalidDirectoryException($message);
        }
    }
}
