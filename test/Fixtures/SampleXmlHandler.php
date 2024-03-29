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

use Psr\Http\Message\StreamInterface;
use RunOpenCode\Sax\Handler\AbstractSaxHandler;

/**
 * @phpstan-import-type SaxHandlerConfiguration from \RunOpenCode\Sax\Handler\AbstractSaxHandler
 */
class SampleXmlHandler extends AbstractSaxHandler
{
    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $output;

    /**
     * @param SaxHandlerConfiguration $options
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->output = [];
    }

    /**
     * {@inheritdoc}
     */
    protected function onDocumentStart(\XMLParser $parser, StreamInterface $stream): void
    {
        $this->output[] = ['event' => 'onDocumentStart'];
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed[] $attributes
     */
    protected function onElementStart(\XMLParser $parser, string $name, array $attributes): void
    {
        $this->output[] = ['event' => 'onElementStart', 'tagName' => $name, 'attributes' => $attributes];
    }

    /**
     * {@inheritdoc}
     */
    protected function onElementData(\XMLParser $parser, string $data): void
    {
        if (\trim($data)) {
            $this->output[] = ['event' => 'onElementData', 'data' => trim($data)];
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function onElementEnd(\XMLParser $parser, string $name): void
    {
        $this->output[] = ['event' => 'onElementEnd', 'tagName' => $name];
    }

    /**
     * {@inheritdoc}
     */
    protected function onDocumentEnd(\XMLParser $parser, StreamInterface $stream): void
    {
        $this->output[] = ['event' => 'onDocumentStart'];
    }

    /**
     * {@inheritdoc}
     */
    protected function onParseError(string $message, int $code, int $lineno): void
    {
        throw new \RuntimeException(sprintf('Parser error "%s", lineno: %s', strtolower($message), $lineno), $code);
    }

    /**
     * {@inheritdoc}
     */
    protected function getResult(): mixed
    {
        return $this->output;
    }
}
