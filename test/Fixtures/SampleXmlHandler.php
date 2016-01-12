<?php
namespace RunOpenCode\Sax\Test\Fixtures;

use Psr\Http\Message\StreamInterface;
use RunOpenCode\Sax\Handler\AbstractSaxHandler;

class SampleXmlHandler extends AbstractSaxHandler
{
    /**
     * {@inheritdoc}
     */
    protected function onDocumentStart($parser, $stream)
    {

    }

    /**
     * {@inheritdoc}
     */
    protected function onElementStart($parser, $name, $attributes)
    {

    }

    /**
     * {@inheritdoc}
     */
    protected function onElementData($parser, $data)
    {

    }

    /**
     * {@inheritdoc}
     */
    protected function onElementEnd($parser, $name)
    {

    }

    /**
     * {@inheritdoc}
     */
    protected function onDocumentEnd($parser, $stream)
    {

    }

    /**
     * {@inheritdoc}
     */
    protected function onParseError($message, $code, $lineno)
    {

    }

    /**
     * {@inheritdoc}
     */
    protected function onResult(callable $callable = null)
    {

    }
}
