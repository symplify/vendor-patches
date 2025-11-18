<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace VendorPatches202511\Symfony\Component\Console\Input;

use VendorPatches202511\Symfony\Component\Console\Command\Command;
use VendorPatches202511\Symfony\Component\Console\Completion\CompletionInput;
use VendorPatches202511\Symfony\Component\Console\Completion\CompletionSuggestions;
use VendorPatches202511\Symfony\Component\Console\Completion\Suggestion;
use VendorPatches202511\Symfony\Component\Console\Exception\InvalidArgumentException;
use VendorPatches202511\Symfony\Component\Console\Exception\LogicException;
/**
 * Represents a command line argument.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class InputArgument
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $description = '';
    /**
     * @var array|\Closure(CompletionInput, CompletionSuggestions):(list<string|Suggestion>)
     */
    private $suggestedValues = [];
    /**
     * Providing an argument is required (e.g. just 'app:foo' is not allowed).
     */
    public const REQUIRED = 1;
    /**
     * Providing an argument is optional (e.g. 'app:foo' and 'app:foo bar' are both allowed). This is the default behavior of arguments.
     */
    public const OPTIONAL = 2;
    /**
     * The argument accepts multiple values and turn them into an array (e.g. 'app:foo bar baz' will result in value ['bar', 'baz']).
     */
    public const IS_ARRAY = 4;
    /**
     * @var int
     */
    private $mode;
    /**
     * @var mixed[]|bool|float|int|string|null
     */
    private $default;
    /**
     * @param string                                                                        $name            The argument name
     * @param int-mask-of<InputArgument::*>|null                                            $mode            The argument mode: a bit mask of self::REQUIRED, self::OPTIONAL and self::IS_ARRAY
     * @param string                                                                        $description     A description text
     * @param string|bool|int|float|array|null                                              $default         The default value (for self::OPTIONAL mode only)
     * @param array|\Closure(CompletionInput,CompletionSuggestions):list<string|Suggestion> $suggestedValues The values used for input completion
     *
     * @throws InvalidArgumentException When argument mode is not valid
     */
    public function __construct(string $name, ?int $mode = null, string $description = '', $default = null, $suggestedValues = [])
    {
        $this->name = $name;
        $this->description = $description;
        $this->suggestedValues = $suggestedValues;
        if (null === $mode) {
            $mode = self::OPTIONAL;
        } elseif ($mode >= self::IS_ARRAY << 1 || $mode < 1) {
            throw new InvalidArgumentException(\sprintf('Argument mode "%s" is not valid.', $mode));
        }
        $this->mode = $mode;
        $this->setDefault($default);
    }
    /**
     * Returns the argument name.
     */
    public function getName() : string
    {
        return $this->name;
    }
    /**
     * Returns true if the argument is required.
     *
     * @return bool true if parameter mode is self::REQUIRED, false otherwise
     */
    public function isRequired() : bool
    {
        return self::REQUIRED === (self::REQUIRED & $this->mode);
    }
    /**
     * Returns true if the argument can take multiple values.
     *
     * @return bool true if mode is self::IS_ARRAY, false otherwise
     */
    public function isArray() : bool
    {
        return self::IS_ARRAY === (self::IS_ARRAY & $this->mode);
    }
    /**
     * Sets the default value.
     * @param string|bool|int|float|mixed[]|null $default
     */
    public function setDefault($default) : void
    {
        if ($this->isRequired() && null !== $default) {
            throw new LogicException('Cannot set a default value except for InputArgument::OPTIONAL mode.');
        }
        if ($this->isArray()) {
            if (null === $default) {
                $default = [];
            } elseif (!\is_array($default)) {
                throw new LogicException('A default value for an array argument must be an array.');
            }
        }
        $this->default = $default;
    }
    /**
     * Returns the default value.
     * @return mixed[]|bool|float|int|string|null
     */
    public function getDefault()
    {
        return $this->default;
    }
    /**
     * Returns true if the argument has values for input completion.
     */
    public function hasCompletion() : bool
    {
        return [] !== $this->suggestedValues;
    }
    /**
     * Supplies suggestions when command resolves possible completion options for input.
     *
     * @see Command::complete()
     */
    public function complete(CompletionInput $input, CompletionSuggestions $suggestions) : void
    {
        $values = $this->suggestedValues;
        if ($values instanceof \Closure && !\is_array($values = $values($input))) {
            throw new LogicException(\sprintf('Closure for argument "%s" must return an array. Got "%s".', $this->name, \get_debug_type($values)));
        }
        if ($values) {
            $suggestions->suggestValues($values);
        }
    }
    /**
     * Returns the description text.
     */
    public function getDescription() : string
    {
        return $this->description;
    }
}
