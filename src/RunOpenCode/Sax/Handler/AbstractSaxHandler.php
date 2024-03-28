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
 *
 * @phpstan-type SaxHandlerOptions = array{
 *      buffer_size: int,
 *      case_folding: bool,
 *      separator: string,
 *      encoding: string,
 *      skip_tagstart: int|null,
 *      skip_white: int|null
 * }
 * @phpstan-type SaxHandlerConfiguration = array{
 *       buffer_size?: int,
 *       case_folding?: bool,
 *       separator?: string,
 *       encoding?: string,
 *       skip_tagstart?: int|null,
 *       skip_white?: int|null
 *  }
 *
 * @deprecated Use RunOpenCode\Sax\Handler\AbstractStackedSaxHandler instead.
 */
abstract class AbstractSaxHandler implements SaxHandlerInterface
{
    /**
     * @var SaxHandlerOptions
     */
    protected array $options;

    private ?string $currentElement = null;

    private int $stackSize = 0;

    private ?string $dataBuffer = null;

    /**
     * @var string[]
     */
    private array $namespaces = [];

    /**
     * @var callable|null
     */
    protected mixed $callback = null;

    /**
     * @param SaxHandlerConfiguration $options
     */
    public function __construct(array $options = [])
    {
        /**
         * @psalm-suppress InvalidPropertyAssignmentValue
         */
        $this->options = \array_merge([
            'buffer_size' => 4096,
            'case_folding' => true,
            'separator' => ':',
            'encoding' => 'UTF-8',
            'skip_tagstart' => null,
            'skip_white' => null,
        ], $options);
    }

    /**
     * {@inheritdoc}
     */
    final public function parse(StreamInterface $stream, ?callable $callback = null): mixed
    {

        $this->callback = $callback;
        $encoding = $this->options['encoding'];
        $separator = $this->options['separator'];

        $parser = \xml_parser_create_ns($encoding, $separator);

        if (false === $this->options['case_folding']) {
            \xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        }

        if (null !== $this->options['skip_tagstart']) {
            \xml_parser_set_option($parser, XML_OPTION_SKIP_TAGSTART, $this->options['skip_tagstart']);
        }

        if (null !== $this->options['skip_white']) {
            \xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, $this->options['skip_white']);
        }

        $this->onDocumentStart($parser, $stream);

        $this->attachHandlers($parser);

        $this->process($parser, $stream);

        $this->onDocumentEnd($parser, $stream);

        \xml_parser_free($parser);

        $stream->close();

        return $this->getResult();
    }

    public function getCurrentElement(): ?string
    {
        return $this->currentElement;
    }

    /**
     * Document start handler, executed when parsing process started.
     */
    abstract protected function onDocumentStart(\XMLParser $parser, StreamInterface $stream): void;

    /**
     * Element start handler, executed when XML tag is entered.
     *
     * @param string[] $attributes
     */
    abstract protected function onElementStart(\XMLParser $parser, string $name, array $attributes): void;

    /**
     * Element CDATA handler, executed when XML tag CDATA is parsed.
     */
    abstract protected function onElementData(\XMLParser $parser, string $data): void;

    /**
     * Element end handler, executed when XML tag is leaved.
     */
    abstract protected function onElementEnd(\XMLParser $parser, string $name): void;

    /**
     * Document end handler, executed when parsing process ended.
     */
    abstract protected function onDocumentEnd(\XMLParser $parser, StreamInterface $stream): void;

    /**
     * Parsing error handler.
     */
    abstract protected function onParseError(string $message, int $code, int $lineno): void;

    /**
     * Get parsing result.
     *
     * Considering that your handler processed XML document, this method will collect
     * parsing result. This method is called last and it will provide parsing result to invoker.
     *
     * @return mixed Parsing result
     */
    abstract protected function getResult(): mixed;

    /**
     * Start namespace declaration handler, executed when namespace declaration started.
     */
    protected function onNamespaceDeclarationStart(\XMLParser $parser, string $prefix, string $uri): void
    {
        // noop
    }

    /**
     * End namespace declaration handler, executed when namespace declaration ended.
     */
    protected function onNamespaceDeclarationEnd(\XMLParser $parser, string $prefix): void
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
     * @return string[]
     */
    final protected function getDeclaredNamespaces(): array
    {
        return $this->namespaces;
    }

    /**
     * Parse path to XML document/string content.
     *
     * @throws \RuntimeException
     */
    private function process(\XMLParser $parser, StreamInterface $stream): self
    {
        if ($stream->eof()) {
            $stream->rewind();
        }

        /**
         * @var ?int $bufferSize
         */
        $bufferSize = $this->options['buffer_size'];

        \assert(null !== $bufferSize);

        while ($data = $stream->read($bufferSize)) {
            /**
             * @phpstan-ignore-next-line
             * @psalm-suppress PossiblyNullArgument
             */
            \xml_parse($parser, $data, $stream->eof()) || $this->onParseError(\xml_error_string(\xml_get_error_code($parser)), \xml_get_error_code($parser), \xml_get_current_line_number($parser));
        }

        return $this;
    }

    /**
     * Attach handlers.
     *
     * @return AbstractSaxHandler $this Fluent interface.
     */
    private function attachHandlers(\XMLParser $parser): self
    {
        /**
         * @param string[] $attributes
         * @var \Closure $onElementStart
         */
        $onElementStart = \Closure::bind(function (\XMLParser $parser, string $name, array $attributes): void {
            $name = $this->normalize($name);
            $this->currentElement = $name;
            $this->dataBuffer = null;

            $this->stackSize++;

            $this->onElementStart($parser, $name, $attributes);
        }, $this);

        /**
         * @var \Closure $onElementEnd
         */
        $onElementEnd = \Closure::bind(function (\XMLParser $parser, string $name): void {
            $name = $this->normalize($name);


            if (null !== $this->dataBuffer) {
                $this->onElementData($parser, $this->dataBuffer);
            }

            $this->dataBuffer = null;

            $this->onElementEnd($parser, $name);
            $this->stackSize--;
            $this->currentElement = null;

        }, $this);

        /**
         * @var \Closure $onElementData
         */
        $onElementData = \Closure::bind(function (\XMLParser $parser, ?string $data): void {
            $this->dataBuffer .= $data;
        }, $this);

        /**
         * @var \Closure $onNamespaceDeclarationStart
         */
        $onNamespaceDeclarationStart = \Closure::bind(function (\XMLParser $parser, string $prefix, string $uri): void {
            $this->namespaces[$prefix] = \rtrim($uri, '/');
            $this->onNamespaceDeclarationStart($parser, $prefix, $uri);
        }, $this);

        /**
         * @var \Closure $onNamespaceDeclarationEnd
         */
        $onNamespaceDeclarationEnd = \Closure::bind(function (\XMLParser $parser, string $prefix): void {
            $this->onNamespaceDeclarationEnd($parser, $prefix);
        }, $this);

        \xml_set_element_handler($parser, $onElementStart, $onElementEnd);

        \xml_set_character_data_handler($parser, $onElementData);

        \xml_set_start_namespace_decl_handler($parser, $onNamespaceDeclarationStart);

        \xml_set_end_namespace_decl_handler($parser, $onNamespaceDeclarationEnd);

        return $this;
    }

    /**
     * Normalize namespaced tag name.
     */
    private function normalize(string $name): string
    {
        return \str_replace('/:', ':', $name);
    }
}
