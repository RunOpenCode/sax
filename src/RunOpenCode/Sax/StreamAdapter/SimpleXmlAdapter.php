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

use Psr\Http\Message\StreamInterface;
use RunOpenCode\Sax\Contract\StreamAdapterInterface;

class SimpleXmlAdapter implements StreamAdapterInterface
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
     * SimpleXmlAdapter constructor.
     *
     * @param string $streamClass FQCN of StreamInterface implementation, GuzzleHttp\Stream\Stream is used by default.
     * @param array $options Adapter options.
     */
    public function __construct($streamClass = 'GuzzleHttp\\Stream\\Stream', array $options = array())
    {
        $this->streamClass = $streamClass;
        $this->options = array_merge(array(
            'stream' => 'php://memory'
        ), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($xmlDocument)
    {
        return $xmlDocument instanceof \SimpleXMLElement;
    }

    /**
     * {@inheritdoc}
     */
    public function convert($xmlDocument)
    {
        /**
         * @var \SimpleXMLElement $xmlDocument
         */
        if (class_exists($this->streamClass)) {

            $stream = fopen($this->options['stream'],'r+');
            fwrite($stream, $xmlDocument->asXML());
            rewind($stream);

            return new $this->streamClass($xmlDocument);
        }

        throw new \RuntimeException(sprintf('Provided StreamInterface implementation "%s" is not available on this system.', $this->streamClass));
    }
}