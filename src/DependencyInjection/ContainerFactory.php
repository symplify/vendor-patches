<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\DependencyInjection;

use Entropy\Container\Container;

final class ContainerFactory
{
    public static function create(): Container
    {
        $container = new Container();

        $container->autodiscover(__DIR__ . '/../Command');

        return $container;
    }
}
