<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Sax\Handler;

/**
 * Class AbstractStackedSaxHandler
 *
 * Sax handler prototype with implemented elements stack.
 *
 * @package RunOpenCode\Sax\Handler
 *
 * @psalm-suppress DeprecatedClass
 */
abstract class AbstractStackedSaxHandler extends AbstractSaxHandler
{
    /**
     * Elements stack
     *
     * @var string[]
     */
    private array $stack = [];

    /**
     * {@inheritdoc}
     */
    protected function onElementStart(\XMLParser $parser, string $name, array $attributes): void
    {
        $this->stack[] = $name;
        $this->handleOnElementStart($parser, $name, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    protected function onElementEnd(\XMLParser $parser, string $name): void
    {
        \array_pop($this->stack);
        $this->handleOnElementEnd($parser, $name);
    }

    /**
     * Get current processing element name (uppercase), or null, if there is no element on stack
     * (processing didn't started or it is ended)
     */
    protected function getCurrentElementName(): ?string
    {
        return (($count = count($this->stack)) > 0) ? $this->stack[$count-1] : null;
    }

    /**
     * Get current stack trace.
     *
     * @return mixed[]
     */
    protected function getStack(): array
    {
        return $this->stack;
    }

    /**
     * Get current element stack size
     */
    protected function getStackSize(): int
    {
        return count($this->stack);
    }

    /**
     * Element start handler, executed when XML tag is entered.
     *
     * @param mixed[] $attributes
     */
    abstract protected function handleOnElementStart(\XMLParser $parser, string $name, array $attributes): void;

    /**
     * Element end handler, executed when XML tag is leaved.
     */
    abstract protected function handleOnElementEnd(\XMLParser $parser, string $name): void;
}
