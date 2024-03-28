<?php

declare(strict_types=1);

namespace RunOpenCode\Sax\Test\Fixtures;

use Psr\Http\Message\StreamInterface;
use RunOpenCode\Sax\Handler\AbstractStackedSaxHandler;

/**
 * @phpstan-import-type SaxHandlerConfiguration from \RunOpenCode\Sax\Handler\AbstractSaxHandler
 */
final class CallbackStackedXmlHandler extends AbstractStackedSaxHandler
{
    /**
     * @var array<int, mixed>
     */
    protected array $output;

    /**
     * @var array<string, mixed>
     */
    private ?array $itemData = null;

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
        // NOOP
    }

    /**
     * {@inheritdoc}
     *
     * @param string[] $attributes
     */
    protected function onElementStart(\XMLParser $parser, string $name, array $attributes): void
    {
        parent::onElementStart($parser, $name, $attributes);

        if ('item' === \strtolower($name)) {
            $this->itemData = [];
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function onElementData(\XMLParser $parser, string $data): void
    {
        if (\trim($data)) {
            $this->output[] = $this->getCurrentElementName();
        }

        if (null === $this->itemData) {
            return;
        }

        match (\strtolower($this->getCurrentElement() ?? '')) {
            'name', 'id' => $this->itemData[\strtolower($this->getCurrentElement())] = \trim($data),
            default => null,
        };
    }

    /**
     * {@inheritdoc}
     */
    protected function onElementEnd(\XMLParser $parser, string $name): void
    {
        parent::onElementEnd($parser, $name);

        if ('item' === \strtolower($name)) {
            \assert(\is_callable($this->callback));
            ($this->callback)($this->itemData);
            $this->itemData = null;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function onDocumentEnd(\XMLParser $parser, StreamInterface $stream): void
    {
        //NOOP
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

    /**
     * {@inheritdoc}
     *
     * @param mixed[] $attributes
     */
    protected function handleOnElementStart(\XMLParser $parser, string $name, array $attributes): void
    {
        $this->output[] = $this->getStackSize();
    }

    /**
     * {@inheritdoc}
     */
    protected function handleOnElementEnd(\XMLParser $parser, string $name): void
    {
        $this->output[] = $this->getStackSize();
    }
}