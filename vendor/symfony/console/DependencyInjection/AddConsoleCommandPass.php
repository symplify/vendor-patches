<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace VendorPatches202511\Symfony\Component\Console\DependencyInjection;

use VendorPatches202511\Symfony\Component\Console\Attribute\AsCommand;
use VendorPatches202511\Symfony\Component\Console\Command\Command;
use VendorPatches202511\Symfony\Component\Console\Command\LazyCommand;
use VendorPatches202511\Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use VendorPatches202511\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use VendorPatches202511\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use VendorPatches202511\Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use VendorPatches202511\Symfony\Component\DependencyInjection\ContainerBuilder;
use VendorPatches202511\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use VendorPatches202511\Symfony\Component\DependencyInjection\Reference;
use VendorPatches202511\Symfony\Component\DependencyInjection\TypedReference;
/**
 * Registers console commands.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class AddConsoleCommandPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container) : void
    {
        $commandServices = $container->findTaggedServiceIds('console.command', \true);
        $lazyCommandMap = [];
        $lazyCommandRefs = [];
        $serviceIds = [];
        foreach ($commandServices as $id => $tags) {
            $definition = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($definition->getClass());
            if (!($r = $container->getReflectionClass($class))) {
                throw new InvalidArgumentException(\sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id));
            }
            if (!$r->isSubclassOf(Command::class)) {
                if (!$r->hasMethod('__invoke')) {
                    throw new InvalidArgumentException(\sprintf('The service "%s" tagged "%s" must either be a subclass of "%s" or have an "__invoke()" method.', $id, 'console.command', Command::class));
                }
                $invokableRef = new Reference($id);
                $definition = $container->register($id .= '.command', $class = Command::class)->addMethodCall('setCode', [$invokableRef]);
            } else {
                $invokableRef = null;
            }
            $definition->addTag('container.no_preload');
            /** @var AsCommand|null $attribute */
            $attribute = ($nullsafeVariable1 = $r->getAttributes(AsCommand::class)[0] ?? null) ? $nullsafeVariable1->newInstance() : null;
            if (Command::class !== (new \ReflectionMethod($class, 'getDefaultName'))->class) {
                trigger_deprecation('symfony/console', '7.3', 'Overriding "Command::getDefaultName()" in "%s" is deprecated and will be removed in Symfony 8.0, use the #[AsCommand] attribute instead.', $class);
                $defaultName = $class::getDefaultName();
            } else {
                $defaultName = ($nullsafeVariable2 = $attribute) ? $nullsafeVariable2->name : null;
            }
            $aliases = \str_replace('%', '%%', $tags[0]['command'] ?? $defaultName ?? '');
            $aliases = \explode('|', $aliases);
            $commandName = \array_shift($aliases);
            if ($isHidden = '' === $commandName) {
                $commandName = \array_shift($aliases);
            }
            if (null === $commandName) {
                if (!$definition->isPublic() || $definition->isPrivate() || $definition->hasTag('container.private')) {
                    $commandId = 'console.command.public_alias.' . $id;
                    $container->setAlias($commandId, $id)->setPublic(\true);
                    $id = $commandId;
                }
                $serviceIds[] = $id;
                continue;
            }
            $description = $tags[0]['description'] ?? null;
            $help = $tags[0]['help'] ?? null;
            unset($tags[0]);
            $lazyCommandMap[$commandName] = $id;
            $lazyCommandRefs[$id] = new TypedReference($id, $class);
            foreach ($aliases as $alias) {
                $lazyCommandMap[$alias] = $id;
            }
            foreach ($tags as $tag) {
                if (isset($tag['command'])) {
                    $aliases[] = $tag['command'];
                    $lazyCommandMap[$tag['command']] = $id;
                }
                $description = $description ?? $tag['description'] ?? null;
                $help = $help ?? $tag['help'] ?? null;
            }
            $definition->addMethodCall('setName', [$commandName]);
            if ($aliases) {
                $definition->addMethodCall('setAliases', [$aliases]);
            }
            if ($isHidden) {
                $definition->addMethodCall('setHidden', [\true]);
            }
            if ($help && $invokableRef) {
                $definition->addMethodCall('setHelp', [\str_replace('%', '%%', $help)]);
            }
            if (!$description) {
                if (Command::class !== (new \ReflectionMethod($class, 'getDefaultDescription'))->class) {
                    trigger_deprecation('symfony/console', '7.3', 'Overriding "Command::getDefaultDescription()" in "%s" is deprecated and will be removed in Symfony 8.0, use the #[AsCommand] attribute instead.', $class);
                    $description = $class::getDefaultDescription();
                } else {
                    $description = ($nullsafeVariable3 = $attribute) ? $nullsafeVariable3->description : null;
                }
            }
            if ($description) {
                $definition->addMethodCall('setDescription', [\str_replace('%', '%%', $description)]);
                $container->register('.' . $id . '.lazy', LazyCommand::class)->setArguments([$commandName, $aliases, $description, $isHidden, new ServiceClosureArgument($lazyCommandRefs[$id])]);
                $lazyCommandRefs[$id] = new Reference('.' . $id . '.lazy');
            }
        }
        $container->register('console.command_loader', ContainerCommandLoader::class)->setPublic(\true)->addTag('container.no_preload')->setArguments([ServiceLocatorTagPass::register($container, $lazyCommandRefs), $lazyCommandMap]);
        $container->setParameter('console.command.ids', $serviceIds);
    }
}
