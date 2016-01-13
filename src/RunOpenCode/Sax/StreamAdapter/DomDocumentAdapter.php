<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Sax\StreamAdapter;

use RunOpenCode\Sax\Contract\StreamAdapterInterface;

/**
 * Class DomAdapter
 *
 * DOMDocument to stream adapter.
 *
 * @package RunOpenCode\Sax\Contract
 */
class DomDocumentAdapter implements StreamAdapterInterface
{
    /**
     * @var string
     */
    private $streamClass;

    /**
     * @var array
     */
    private $options;

    /**
     * DomDocumentAdapter constructor.
     *
     * @param string $streamClass FQCN of StreamInterface implementation, GuzzleHttp\Psr7\Stream is used by default.
     * @param array $options Adapter options.
     */
    public function __construct($streamClass = 'GuzzleHttp\\Psr7\\Stream', array $options = array())
    {
        $this->streamClass = $streamClass;
        $this->options = array_merge(array(
            'stream' => 'php://memory',
            'save_xml_options' => null
        ), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($xmlDocument)
    {
        return $xmlDocument instanceof \DOMDocument;
    }

    /**
     * {@inheritdoc}
     */
    public function convert($xmlDocument)
    {
        /**
         * @var \DOMDocument $xmlDocument
         */
        if (class_exists($this->streamClass)) {

            $stream = fopen($this->options['stream'],'r+');
            fwrite($stream, $xmlDocument->saveXML($this->options['save_xml_options']));
            rewind($stream);

            return new $this->streamClass($stream);
        }

        throw new \RuntimeException(sprintf('Provided StreamInterface implementation "%s" is not available on this system.', $this->streamClass));
    }
}
