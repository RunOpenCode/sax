<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Sax;

use Psr\Http\Message\StreamInterface;
use RunOpenCode\Sax\Contract\SaxHandlerInterface;
use RunOpenCode\Sax\Contract\StreamAdapterInterface;
use RunOpenCode\Sax\Exception\RuntimeException;
use RunOpenCode\Sax\StreamAdapter\DomDocumentAdapter;
use RunOpenCode\Sax\StreamAdapter\ResourceAdapter;
use RunOpenCode\Sax\StreamAdapter\SimpleXmlAdapter;

/**
 * Class SaxParser
 *
 * Utility class for working with SAX handler and XML document.
 *
 * @package RunOpenCode\Sax
 */
class SaxParser
{
    /**
     * @var StreamAdapterInterface[]
     */
    private $streamAdapters;

    /**
     * SaxParser constructor.
     *
     * @param StreamAdapterInterface[] $streamAdapters Stream adapters to register to parser.
     */
    public function __construct(array $streamAdapters = array())
    {
        $this->streamAdapters = array();

        foreach ($streamAdapters as $streamAdapter) {
            $this->addStreamAdapter($streamAdapter);
        }
    }

    /**
     * Register stream adapter to parser.
     *
     * @param StreamAdapterInterface $streamAdapter Stream adapter to register.
     * @return SaxParser $this Fluent interface.
     */
    public function addStreamAdapter(StreamAdapterInterface $streamAdapter)
    {
        $this->streamAdapters[] = $streamAdapter;
        return $this;
    }

    /**
     * Parse XML document using provided SAX handler.
     *
     * @param SaxHandlerInterface $saxHandler Handler to user for parsing document.
     * @param mixed $xmlDocument XML document source.
     *
     * @return mixed Parsing result.
     */
    public function parse(SaxHandlerInterface $saxHandler, $xmlDocument)
    {
        $xmlDocument = ($xmlDocument instanceof StreamInterface) ? $xmlDocument : $this->getDocumentStream($xmlDocument);
        return $saxHandler->parse($xmlDocument);
    }

    /**
     * Default SAX parser factory.
     *
     * @param string $streamClass FQCN to use when converting to XML document source to stream.
     * @return SaxParser New SAX parser instance.
     */
    public static function factory($streamClass = 'GuzzleHttp\\Psr7\\Stream')
    {
        return new static([
            new ResourceAdapter($streamClass),
            new DomDocumentAdapter($streamClass),
            new SimpleXmlAdapter($streamClass),
        ]);
    }

    /**
     * Convert XML document to stream source.
     *
     * @param mixed $xmlDocument XML document source.
     * @return StreamInterface Converted XML document to stream.
     *
     * @throws \RuntimeException
     */
    private function getDocumentStream($xmlDocument)
    {
        /**
         * @var StreamAdapterInterface $streamAdapter
         */
        foreach ($this->streamAdapters as $streamAdapter) {

            if ($streamAdapter->supports($xmlDocument)) {
                return $streamAdapter->convert($xmlDocument);
            }
        }

        throw new RuntimeException(sprintf('Suitable XML document stream adapter is not registered for XML document of type "%s".', is_object($xmlDocument) ? get_class($xmlDocument) : gettype($xmlDocument)));
    }
}
