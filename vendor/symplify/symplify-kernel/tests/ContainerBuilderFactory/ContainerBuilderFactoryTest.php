<?php

declare (strict_types=1);
namespace VendorPatches202211\Symplify\SymplifyKernel\Tests\ContainerBuilderFactory;

use VendorPatches202211\PHPUnit\Framework\TestCase;
use VendorPatches202211\Symplify\SmartFileSystem\SmartFileSystem;
use VendorPatches202211\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use VendorPatches202211\Symplify\SymplifyKernel\ContainerBuilderFactory;
final class ContainerBuilderFactoryTest extends TestCase
{
    public function test() : void
    {
        $containerBuilderFactory = new ContainerBuilderFactory(new ParameterMergingLoaderFactory());
        $containerBuilder = $containerBuilderFactory->create([__DIR__ . '/config/some_services.php'], [], []);
        $hasSmartFileSystemService = $containerBuilder->has(SmartFileSystem::class);
        $this->assertTrue($hasSmartFileSystemService);
    }
}
