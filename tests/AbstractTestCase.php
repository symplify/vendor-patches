<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\VendorPatches\DependencyInjection\ContainerFactory;
use Webmozart\Assert\Assert;

abstract class AbstractTestCase extends TestCase
{
    /**
     * @template TType as object
     * @param class-string<TType> $type
     * @return TType
     */
    protected function make(string $type): object
    {
        $containerFactory = new ContainerFactory();
        $container = $containerFactory->create();

        $service = $container->make($type);
        Assert::isInstanceOf($service, $type);

        return $service;
    }
}
