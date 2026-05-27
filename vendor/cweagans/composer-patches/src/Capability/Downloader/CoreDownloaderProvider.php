<?php

namespace VendorPatches202605\cweagans\Composer\Capability\Downloader;

use VendorPatches202605\cweagans\Composer\Downloader\ComposerDownloader;
class CoreDownloaderProvider extends BaseDownloaderProvider
{
    /**
     * @inheritDoc
     */
    public function getDownloaders() : array
    {
        return [new ComposerDownloader($this->composer, $this->io, $this->plugin)];
    }
}
