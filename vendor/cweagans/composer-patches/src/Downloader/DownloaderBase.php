<?php

namespace VendorPatches202511\cweagans\Composer\Downloader;

use VendorPatches202511\Composer\Composer;
use VendorPatches202511\Composer\IO\IOInterface;
use VendorPatches202511\Composer\Plugin\PluginInterface;
use VendorPatches202511\cweagans\Composer\Downloader\Exception\HashMismatchException;
use VendorPatches202511\cweagans\Composer\Patch;
abstract class DownloaderBase implements DownloaderInterface
{
    /**
     * The main Composer object.
     *
     * @var Composer
     */
    protected $composer;
    /**
     * An array of operations that will be executed during this composer execution.
     *
     * @var IOInterface
     */
    protected $io;
    /**
     * An instance of the main plugin class.
     *
     * @var PluginInterface
     */
    protected $plugin;
    /**
     * @inheritDoc
     */
    public function __construct(Composer $composer, IOInterface $io, PluginInterface $plugin)
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->plugin = $plugin;
    }
    /**
     * @inheritDoc
     * @throws HashMismatchException
     */
    public abstract function download(Patch $patch) : void;
}
