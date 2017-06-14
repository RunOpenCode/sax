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

use RunOpenCode\Sax\Handler\AbstractStackedSaxHandler;

class SampleStackedXmlHandler extends AbstractStackedSaxHandler
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
        // noop
    }

    /**
     * {@inheritdoc}
     */
    protected function onElementData($parser, $data)
    {
        if (trim($data)) {
            $this->output[] = $this->getCurrentElementName();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function onDocumentEnd($parser, $stream)
    {
        // noop
    }

    /**
     * {@inheritdoc}
     */
    protected function onParseError($message, $code, $lineno)
    {
        // noop
    }

    /**
     * {@inheritdoc}
     */
    protected function getResult()
    {
        return $this->output;
    }

    /**
     * {@inheritdoc}
     */
    protected function handleOnElementStart($parser, $name, $attributes)
    {
        $this->output[] = $this->getStackSize();
    }

    /**
     * {@inheritdoc}
     */
    protected function handleOnElementEnd($parser, $name)
    {
        $this->output[] = $this->getStackSize();
    }
}
