<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\AutoBindParameter\DependencyInjection\CompilerPass\AutoBindParameterCompilerPass;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;

final class VendorPatchesKernel extends Kernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [];
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/vendor_patches_generator';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/vendor_patches_generator_log';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.yaml');
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
        $containerBuilder->addCompilerPass(new AutoBindParameterCompilerPass());
    }
}
