<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\ValueObject;

use Nette\Utils\FileSystem;

final class OldAndNewFile
{
    public function __construct(
        private readonly string $oldFilePath,
        private readonly string $newFilePath,
        private readonly string $packageName
    ) {
    }

    public function getOldFilePath(): string
    {
        return $this->oldFilePath;
    }

    public function getOldFileContents(): string
    {
        return FileSystem::read($this->oldFilePath);
    }

    public function getNewFileContents(): string
    {
        return FileSystem::read($this->newFilePath);
    }

    public function getNewFilePath(): string
    {
        return $this->newFilePath;
    }

    public function areContentsIdentical(): bool
    {
        $newFileContents = FileSystem::read($this->newFilePath);
        $oldFileContents = FileSystem::read($this->oldFilePath);

        return $newFileContents === $oldFileContents;
    }

    public function getPackageName(): string
    {
        return $this->packageName;
    }
}
