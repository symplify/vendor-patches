<?php

declare (strict_types=1);
namespace VendorPatches20220610\Symplify\SymplifyKernel\HttpKernel;

use VendorPatches20220610\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use VendorPatches20220610\Symfony\Component\DependencyInjection\Container;
use VendorPatches20220610\Symfony\Component\DependencyInjection\ContainerInterface;
use VendorPatches20220610\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use VendorPatches20220610\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use VendorPatches20220610\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use VendorPatches20220610\Symplify\SymplifyKernel\ContainerBuilderFactory;
use VendorPatches20220610\Symplify\SymplifyKernel\Contract\LightKernelInterface;
use VendorPatches20220610\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use VendorPatches20220610\Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig;
/**
 * @api
 */
abstract class AbstractSymplifyKernel implements LightKernelInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container|null
     */
    private $container = null;
    /**
     * @param string[] $configFiles
     * @param CompilerPassInterface[] $compilerPasses
     * @param ExtensionInterface[] $extensions
     */
    public function create(array $configFiles, array $compilerPasses = [], array $extensions = []) : ContainerInterface
    {
        $containerBuilderFactory = new ContainerBuilderFactory(new ParameterMergingLoaderFactory());
        $compilerPasses[] = new AutowireArrayParameterCompilerPass();
        $configFiles[] = SymplifyKernelConfig::FILE_PATH;
        $containerBuilder = $containerBuilderFactory->create($configFiles, $compilerPasses, $extensions);
        $containerBuilder->compile();
        $this->container = $containerBuilder;
        return $containerBuilder;
    }
    public function getContainer() : \VendorPatches20220610\Psr\Container\ContainerInterface
    {
        if (!$this->container instanceof Container) {
            throw new ShouldNotHappenException();
        }
        return $this->container;
    }
}