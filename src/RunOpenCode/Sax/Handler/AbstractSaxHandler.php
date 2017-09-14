<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Sax\Handler;

use Psr\Http\Message\StreamInterface;
use RunOpenCode\Sax\Contract\SaxHandlerInterface;
use RunOpenCode\Sax\Exception\RuntimeException;

/**
 * Class AbstractSaxHandler
 *
 * Sax handler prototype.
 *
 * @package RunOpenCode\Sax
 */
abstract class AbstractSaxHandler implements SaxHandlerInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * AbstractSaxHandler constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = array_merge(array(
            'buffer_size'   => 4096,
            'case_folding'  => true,
            'namespaces'    => false,
            'separator'     => ':',
            'encoding'      => null,
            'skip_tagstart' => null,
            'skip_white'    => null,
        ), $options);
    }

    /**
     * {@inheritdoc}
     */
    final public function parse(StreamInterface $stream)
    {
        $parser = (true === $this->options['namespaces'])
            ?
            xml_parser_create_ns($this->options['encoding'], $this->options['separator'])
            :
            xml_parser_create($this->options['encoding']);

        if (false === $this->options['case_folding']) {
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        }

        if (null === $this->options['skip_tagstart']) {
            xml_parser_set_option($parser, XML_OPTION_SKIP_TAGSTART, $this->options['skip_tagstart']);
        }

        if (null === $this->options['skip_white']) {
            xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, $this->options['skip_white']);
        }

        $this->onDocumentStart($parser, $stream);

        $this->attachHandlers($parser);

        $this->process($parser, $stream);

        $this->onDocumentEnd($parser, $stream);

        xml_parser_free($parser);

        $stream->close();

        return $this->getResult();
    }

    /**
     * Document start handler, executed when parsing process started.
     *
     * @param resource $parser Parser handler.
     * @param StreamInterface $stream XML stream.
     */
    abstract protected function onDocumentStart($parser, $stream);

    /**
     * Element start handler, executed when XML tag is entered.
     *
     * @param resource $parser Parser handler.
     * @param string $name Tag name.
     * @param array $attributes Element attributes.
     */
    abstract protected function onElementStart($parser, $name, $attributes);

    /**
     * Element CDATA handler, executed when XML tag CDATA is parsed.
     *
     * @param resource $parser Parser handler.
     * @param string $data Element CDATA.
     */
    abstract protected function onElementData($parser, $data);

    /**
     * Element end handler, executed when XML tag is leaved.
     *
     * @param resource $parser Parser handler.
     * @param string $name Tag name.
     */
    abstract protected function onElementEnd($parser, $name);

    /**
     * Document end handler, executed when parsing process ended.
     *
     * @param resource $parser Parser handler.
     * @param StreamInterface $stream XML stream.
     */
    abstract protected function onDocumentEnd($parser, $stream);

    /**
     * Start namespace declaration handler, executed when namespace declaration started.
     *
     * @param resource $parser Parser handler.
     * @param string $prefix Namespace reference within an XML object.
     * @param string $uri Uniform Resource Identifier (URI) of namespace.
     */
    protected function onNamespaceDeclarationStart($parser, $prefix, $uri)
    {
        throw new RuntimeException(sprintf('When namespace support is on, method "%s" must be overridden.', __METHOD__));
    }

    /**
     * End namespace declaration handler, executed when namespace declaration ended.
     *
     * @param resource $parser Parser handler.
     * @param string $prefix Namespace reference within an XML object.
     */
    protected function onNamespaceDeclarationEnd($parser , string $prefix)
    {
        throw new RuntimeException(sprintf('When namespace support is on, method "%s" must be overridden.', __METHOD__));
    }

    /**
     * Parsing error handler.
     *
     * @param string $message Parsing error message.
     * @param int $code Error code.
     * @param int $lineno XML line number which caused error.
     */
    abstract protected function onParseError($message, $code, $lineno);

    /**
     * Get parsing result.
     *
     * Considering that your handler processed XML document, this method will collect
     * parsing result. This method is called last and it will provide parsing result to invoker.
     *
     * @return mixed Parsing result
     */
    abstract protected function getResult();

    /**
     * Parse path to XML document/string content.
     *
     * @param resource $parser Parser.
     * @param StreamInterface $stream XML document stream.
     * @return AbstractSaxHandler $this Fluent interface.
     *
     * @throws \RuntimeException
     */
    private function process($parser, StreamInterface $stream)
    {
        if ($stream->eof()) {
            $stream->rewind();
        }

        while ($data = $stream->read($this->options['buffer_size'])) {
            xml_parse($parser, $data, $stream->eof()) || $this->onParseError(xml_error_string(xml_get_error_code($parser)), xml_get_error_code($parser), xml_get_current_line_number($parser));
        }

        return $this;
    }

    /**
     * Attach handlers.
     *
     * @param resource $parser XML parser.
     * @return AbstractSaxHandler $this Fluent interface.
     */
    private function attachHandlers($parser)
    {
        $onElementStart = \Closure::bind(function ($parser, $name, $attributes) {
            $this->onElementStart($parser, $name, $attributes);
        }, $this);

        $onElementEnd = \Closure::bind(function ($parser, $name) {
            $this->onElementEnd($parser, $name);
        }, $this);

        $onElementData =  \Closure::bind(function ($parser, $data) {
            $this->onElementData($parser, $data);
        }, $this);

        xml_set_element_handler($parser, $onElementStart, $onElementEnd);

        xml_set_character_data_handler($parser, $onElementData);

        return $this;
    }
}
