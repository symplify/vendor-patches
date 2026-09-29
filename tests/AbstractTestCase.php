<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\VendorPatches\DependencyInjection\ContainerFactory;

abstract class AbstractTestCase extends TestCase
{
    /**
     * @template TType as object
     * @param class-string<TType> $type
     * @return TType
     */
    protected function make(string $type): object
    {
        $container = ContainerFactory::create();

        $service = $container->make($type);
        $this->assertInstanceOf($type, $service);

        return $service;
    }
}
