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
     * @var string|null
     */
    private $currentElement = null;

    /**
     * @var int
     */
    private $stackSize = 0;

    /**
     * @var string|null
     */
    private $dataBuffer = null;

    /**
     * @var array
     */
    private $namespaces = [];

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
            'separator'     => ':',
            'encoding'      => 'UTF-8',
            'skip_tagstart' => null,
            'skip_white'    => null,
        ), $options);
    }

    /**
     * {@inheritdoc}
     */
    final public function parse(StreamInterface $stream)
    {
        $parser = xml_parser_create_ns($this->options['encoding'], $this->options['separator']);

        if (false === $this->options['case_folding']) {
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        }

        if (null === $this->options['skip_tagstart']) {
            xml_parser_set_option($parser, XML_OPTION_SKIP_TAGSTART, $this->options['skip_tagstart']);
        }

        if (null === $this->options['skip_white']) {
            xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, $this->options['skip_white']);
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
     * Start namespace declaration handler, executed when namespace declaration started.
     *
     * @param resource $parser Parser handler.
     * @param string $prefix Namespace reference within an XML object.
     * @param string $uri Uniform Resource Identifier (URI) of namespace.
     */
    protected function onNamespaceDeclarationStart($parser, $prefix, $uri)
    {
        // noop
    }

    /**
     * End namespace declaration handler, executed when namespace declaration ended.
     *
     * @param resource $parser Parser handler.
     * @param string $prefix Namespace reference within an XML object.
     */
    protected function onNamespaceDeclarationEnd($parser, $prefix)
    {
        // noop
    }

    /**
     * Get declared namespaces.
     *
     * Retrieve declared namespaces as associative array where keys are
     * used prefixes within XML document. Note that only processed namespace
     * declarations will be provided.
     *
     * @return array
     */
    final protected function getDeclaredNamespaces()
    {
        return $this->namespaces;
    }

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
            $name                 = $this->normalize($name);
            $this->currentElement = $name;
            $this->dataBuffer     = null;

            $this->stackSize++;

            $this->onElementStart($parser, $name, $attributes);
        }, $this);

        $onElementEnd   = \Closure::bind(function ($parser, $name) {
            $name                 = $this->normalize($name);
            $this->currentElement = null;

            $this->stackSize--;

            if (null !== $this->dataBuffer) {
                $this->onElementData($parser, $this->dataBuffer);
            }

            $this->dataBuffer = null;

            $this->onElementEnd($parser, $name);
        }, $this);

        $onElementData  =  \Closure::bind(function ($parser, $data) {
            $this->dataBuffer .= $data;
        }, $this);

        $onNamespaceDeclarationStart = \Closure::bind(function ($parser, $prefix, $uri) {
            $this->namespaces[$prefix] = rtrim($uri, '/');
            $this->onNamespaceDeclarationStart($parser, $prefix, $uri);
        }, $this);

        $onNamespaceDeclarationEnd = \Closure::bind(function ($parser, $prefix) {
            $this->onNamespaceDeclarationEnd($parser, $prefix);
        }, $this);

        xml_set_element_handler($parser, $onElementStart, $onElementEnd);

        xml_set_character_data_handler($parser, $onElementData);

        xml_set_start_namespace_decl_handler($parser, $onNamespaceDeclarationStart);

        xml_set_end_namespace_decl_handler($parser, $onNamespaceDeclarationEnd);

        return $this;
    }

    /**
     * Normalize namespaced tag name.
     *
     * @param $name
     *
     * @return string
     */
    private function normalize($name)
    {
        return str_replace('/:', ':', $name);
    }
}
