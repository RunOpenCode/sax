<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Sax\Test\Fixtures;

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
        if (trim($data)) {
            $this->output[] = array('event' => 'onElementData', 'data' => trim($data));
        }
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
        throw new \RuntimeException(sprintf('Parser error "%s", lineno: %s', strtolower($message), $lineno), $code);
    }

    /**
     * {@inheritdoc}
     */
    protected function getResult()
    {
        return $this->output;
    }
}
