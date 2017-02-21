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
 */
abstract class AbstractStackedSaxHandler extends AbstractSaxHandler
{
    /**
     * @var array Elements stack
     */
    private $stack = [];

    /**
     * {@inheritdoc}
     */
    protected function onElementStart($parser, $name, $attributes)
    {
        array_push($this->stack, $name);
        $this->handleOnElementStart($parser, $name, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    protected function onElementEnd($parser, $name)
    {
        array_pop($this->stack);
        $this->handleOnElementEnd($parser, $name);
    }

    /**
     * Get current processing element name (uppercase), or null, if there is no element on stack
     * (processing didn't started or it is ended)
     *
     * @return string|null
     */
    protected function getCurrentElementName()
    {
        return (($count = count($this->stack)) > 0) ? $this->stack[$count-1] : null;
    }

    /**
     * Get current element stack size
     *
     * @return int
     */
    protected function getStackSize()
    {
        return count($this->stack);
    }

    /**
     * Element start handler, executed when XML tag is entered.
     *
     * @param resource $parser Parser handler.
     * @param string $name Tag name.
     * @param array $attributes Element attributes.
     */
    abstract protected function handleOnElementStart($parser, $name, $attributes);

    /**
     * Element end handler, executed when XML tag is leaved.
     *
     * @param resource $parser Parser handler.
     * @param string $name Tag name.
     */
    abstract protected function handleOnElementEnd($parser, $name);
}
