<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Sax\StreamAdapter;

use RunOpenCode\Sax\Contract\StreamAdapterInterface;
use RunOpenCode\Sax\Exception\StreamAdapterException;

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
            'save_xml_options' => null,
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
        $stream = @fopen($this->options['stream'], 'r+b');

        if (false === $stream) {
            throw new StreamAdapterException(sprintf('Unable to acquire resource handler on "%s".', $this->options['stream']));
        }

        fwrite($stream, $xmlDocument->saveXML($this->options['save_xml_options']));

        if (false === @rewind($stream)) {
            throw new StreamAdapterException('Unable to to rewind stream.');
        }

        return new $this->streamClass($stream);
    }
}
