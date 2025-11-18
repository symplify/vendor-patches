<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace VendorPatches202511\Symfony\Component\Console\Attribute;

use VendorPatches202511\Symfony\Component\Console\Completion\CompletionInput;
use VendorPatches202511\Symfony\Component\Console\Completion\Suggestion;
use VendorPatches202511\Symfony\Component\Console\Exception\LogicException;
use VendorPatches202511\Symfony\Component\Console\Input\InputInterface;
use VendorPatches202511\Symfony\Component\Console\Input\InputOption;
use VendorPatches202511\Symfony\Component\String\UnicodeString;
#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Option
{
    /**
     * @var string
     */
    public $description = '';
    /**
     * @var string
     */
    public $name = '';
    /**
     * @var array|string|null
     */
    public $shortcut = null;
    private const ALLOWED_TYPES = ['string', 'bool', 'int', 'float', 'array'];
    private const ALLOWED_UNION_TYPES = ['bool|string', 'bool|int', 'bool|float'];
    /**
     * @var mixed[]|bool|float|int|string|null
     */
    private $default = null;
    /**
     * @var mixed[]|\Closure
     */
    private $suggestedValues;
    /**
     * @var int|null
     */
    private $mode;
    /**
     * @var string
     */
    private $typeName = '';
    /**
     * @var bool
     */
    private $allowNull = \false;
    /**
     * @var string
     */
    private $function = '';
    /**
     * Represents a console command --option definition.
     *
     * If unset, the `name` value will be inferred from the parameter definition.
     *
     * @param array|string|null                                                          $shortcut        The shortcuts, can be null, a string of shortcuts delimited by | or an array of shortcuts
     * @param array<string|Suggestion>|callable(CompletionInput):list<string|Suggestion> $suggestedValues The values used for input completion
     */
    public function __construct(string $description = '', string $name = '', $shortcut = null, $suggestedValues = [])
    {
        $this->description = $description;
        $this->name = $name;
        $this->shortcut = $shortcut;
        $this->suggestedValues = \is_callable($suggestedValues) ? \Closure::fromCallable($suggestedValues) : $suggestedValues;
    }
    /**
     * @internal
     */
    public static function tryFrom(\ReflectionParameter $parameter) : ?self
    {
        /** @var self $self */
        if (null === ($self = ($nullsafeVariable1 = (\method_exists($parameter, 'getAttributes') ? $parameter->getAttributes(self::class, \ReflectionAttribute::IS_INSTANCEOF) : [])[0] ?? null) ? $nullsafeVariable1->newInstance() : null)) {
            return null;
        }
        if (($function = $parameter->getDeclaringFunction()) instanceof \ReflectionMethod) {
            $self->function = $function->class . '::' . $function->name;
        } else {
            $self->function = $function->name;
        }
        $name = $parameter->getName();
        $type = $parameter->getType();
        if (!$parameter->isDefaultValueAvailable()) {
            throw new LogicException(\sprintf('The option parameter "$%s" of "%s()" must declare a default value.', $name, $self->function));
        }
        if (!$self->name) {
            $self->name = (new UnicodeString($name))->kebab();
        }
        $self->default = $parameter->getDefaultValue();
        $self->allowNull = $parameter->allowsNull();
        if ($type instanceof \ReflectionUnionType) {
            return $self->handleUnion($type);
        }
        if (!$type instanceof \ReflectionNamedType) {
            throw new LogicException(\sprintf('The parameter "$%s" of "%s()" must have a named type. Untyped or Intersection types are not supported for command options.', $name, $self->function));
        }
        $self->typeName = $type->getName();
        if (!\in_array($self->typeName, self::ALLOWED_TYPES, \true)) {
            throw new LogicException(\sprintf('The type "%s" on parameter "$%s" of "%s()" is not supported as a command option. Only "%s" types are allowed.', $self->typeName, $name, $self->function, \implode('", "', self::ALLOWED_TYPES)));
        }
        if ('bool' === $self->typeName && $self->allowNull && \in_array($self->default, [\true, \false], \true)) {
            throw new LogicException(\sprintf('The option parameter "$%s" of "%s()" must not be nullable when it has a default boolean value.', $name, $self->function));
        }
        if ($self->allowNull && null !== $self->default) {
            throw new LogicException(\sprintf('The option parameter "$%s" of "%s()" must either be not-nullable or have a default of null.', $name, $self->function));
        }
        if ('bool' === $self->typeName) {
            $self->mode = InputOption::VALUE_NONE;
            if (\false !== $self->default) {
                $self->mode |= InputOption::VALUE_NEGATABLE;
            }
        } elseif ('array' === $self->typeName) {
            $self->mode = InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY;
        } else {
            $self->mode = InputOption::VALUE_REQUIRED;
        }
        if (\is_array($self->suggestedValues) && !\is_callable($self->suggestedValues) && 2 === \count($self->suggestedValues) && ($instance = $parameter->getDeclaringFunction()->getClosureThis()) && \get_class($instance) === $self->suggestedValues[0] && \is_callable([$instance, $self->suggestedValues[1]])) {
            $self->suggestedValues = [$instance, $self->suggestedValues[1]];
        }
        return $self;
    }
    /**
     * @internal
     */
    public function toInputOption() : InputOption
    {
        $default = InputOption::VALUE_NONE === (InputOption::VALUE_NONE & $this->mode) ? null : $this->default;
        $suggestedValues = \is_callable($this->suggestedValues) ? \Closure::fromCallable($this->suggestedValues) : $this->suggestedValues;
        return new InputOption($this->name, $this->shortcut, $this->mode, $this->description, $default, $suggestedValues);
    }
    /**
     * @internal
     * @return mixed
     */
    public function resolveValue(InputInterface $input)
    {
        $value = $input->getOption($this->name);
        if (null === $value && \in_array($this->typeName, self::ALLOWED_UNION_TYPES, \true)) {
            return \true;
        }
        if ('array' === $this->typeName && $this->allowNull && [] === $value) {
            return null;
        }
        if ('bool' !== $this->typeName) {
            return $value;
        }
        if ($this->allowNull && null === $value) {
            return null;
        }
        return $value ?? $this->default;
    }
    private function handleUnion(\ReflectionUnionType $type) : self
    {
        $types = \array_map(static function (\ReflectionType $t) {
            return $t instanceof \ReflectionNamedType ? $t->getName() : null;
        }, $type->getTypes());
        \sort($types);
        $this->typeName = \implode('|', \array_filter($types));
        if (!\in_array($this->typeName, self::ALLOWED_UNION_TYPES, \true)) {
            throw new LogicException(\sprintf('The union type for parameter "$%s" of "%s()" is not supported as a command option. Only "%s" types are allowed.', $this->name, $this->function, \implode('", "', self::ALLOWED_UNION_TYPES)));
        }
        if (\false !== $this->default) {
            throw new LogicException(\sprintf('The option parameter "$%s" of "%s()" must have a default value of false.', $this->name, $this->function));
        }
        $this->mode = InputOption::VALUE_OPTIONAL;
        return $this;
    }
}
