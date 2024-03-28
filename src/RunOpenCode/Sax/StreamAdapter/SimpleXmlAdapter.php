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

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use RunOpenCode\Sax\Contract\StreamAdapterInterface;
use RunOpenCode\Sax\Exception\StreamAdapterException;
use SimpleXMLElement;

/**
 * Class SimpleXmlAdapter
 *
 * SimpleXml adapter
 *
 * @package RunOpenCode\Sax\StreamAdapter
 */
class SimpleXmlAdapter implements StreamAdapterInterface
{
    private string $streamClass;

    /**
     * @var mixed[]
     */
    private array $options;

    /**
     * SimpleXmlAdapter constructor.
     *
     * @param string $streamClass FQCN of StreamInterface implementation, GuzzleHttp\Psr7\Stream is used by default.
     * @param mixed[] $options Adapter options.
     */
    public function __construct(string $streamClass = Stream::class, array $options = [])
    {
        $this->streamClass = $streamClass;
        $this->options = array_merge([
            'stream' => 'php://memory',
        ], $options);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(mixed $xmlDocument): bool
    {
        return $xmlDocument instanceof \SimpleXMLElement;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(mixed $xmlDocument): StreamInterface
    {
        /** @var ?string $optionsStream */
        $optionsStream = $this->options['stream'];

        if (null === $optionsStream) {
            throw new StreamAdapterException('Stream is not provided.');
        }

        $stream = @fopen($optionsStream, 'r+b');

        if (false === $stream) {
            throw new StreamAdapterException(sprintf('Unable to acquire resource handler on "%s".', $optionsStream));
        }

        \assert($xmlDocument instanceof SimpleXMLElement);

        /**
         * @var string $xml
         */
        $xml = $xmlDocument->asXML();

        \fwrite($stream, $xml);

        if (false === @rewind($stream)) {
            throw new StreamAdapterException('Unable to to rewind stream.');
        }

        $object = new $this->streamClass($stream);

        \assert($object instanceof StreamInterface);

        return $object;
    }
}
