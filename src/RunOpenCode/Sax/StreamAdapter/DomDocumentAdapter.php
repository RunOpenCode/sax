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

use DOMNode;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
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
    private string $streamClass;

    /**
     * @var array<string, mixed>
     */
    private array $options;

    /**
     * DomDocumentAdapter constructor.
     *
     * @param string $streamClass FQCN of StreamInterface implementation, GuzzleHttp\Psr7\Stream is used by default.
     * @param mixed[] $options Adapter options.
     */
    public function __construct(string $streamClass = Stream::class, array $options = [])
    {
        $this->streamClass = $streamClass;
        $this->options = array_merge([
            'stream' => 'php://memory',
            'save_xml_options' => null,
        ], $options);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($xmlDocument): bool
    {
        return $xmlDocument instanceof \DOMDocument;
    }

    /**
     * {@inheritdoc}
     */
    public function convert($xmlDocument): StreamInterface
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

        \assert($xmlDocument instanceof \DOMDocument);

        /**
         * @var ?DOMNode $xmlOptions
         */
        $xmlOptions = $this->options['save_xml_options'];

        $data = $xmlDocument->saveXML($xmlOptions);

        \assert(\is_string($data));

        \fwrite($stream, $data);

        if (false === @rewind($stream)) {
            throw new StreamAdapterException('Unable to to rewind stream.');
        }

        $object = new $this->streamClass($stream);

        \assert($object instanceof StreamInterface);

        return $object;
    }
}
