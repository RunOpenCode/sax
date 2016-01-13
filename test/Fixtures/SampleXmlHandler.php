<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Sax\Test\Fixtures;

use Psr\Http\Message\StreamInterface;
use RunOpenCode\Sax\Handler\AbstractSaxHandler;

class SampleXmlHandler extends AbstractSaxHandler
{
    protected $output;

    public function __construct(array $options = array())
    {
        parent::__construct($options);
        $this->output = array();
    }

    /**
     * {@inheritdoc}
     */
    protected function onDocumentStart($parser, $stream)
    {
        $this->output[] = array('event' => 'onDocumentStart');
    }

    /**
     * {@inheritdoc}
     */
    protected function onElementStart($parser, $name, $attributes)
    {
        $this->output[] = array('event' => 'onElementStart', 'tagName' => $name, 'attributes' => $attributes);
    }

    /**
     * {@inheritdoc}
     */
    protected function onElementData($parser, $data)
    {
        $this->output[] = array('event' => 'onElementData', 'data' => $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function onElementEnd($parser, $name)
    {
        $this->output[] = array('event' => 'onElementEnd', 'tagName' => $name);
    }

    /**
     * {@inheritdoc}
     */
    protected function onDocumentEnd($parser, $stream)
    {
        $this->output[] = array('event' => 'onDocumentStart');
    }

    /**
     * {@inheritdoc}
     */
    protected function onParseError($message, $code, $lineno)
    {
        throw new \RuntimeException(sprintf('Parser error "%s", lineno: %s', $message, $lineno), $code);
    }

    /**
     * {@inheritdoc}
     */
    protected function onResult(callable $callable = null)
    {
        $callable($this->output);
    }
}
