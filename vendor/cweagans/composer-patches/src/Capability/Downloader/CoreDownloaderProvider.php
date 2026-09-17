<?php

namespace VendorPatches202609\cweagans\Composer\Capability\Downloader;

use VendorPatches202609\cweagans\Composer\Downloader\ComposerDownloader;
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
