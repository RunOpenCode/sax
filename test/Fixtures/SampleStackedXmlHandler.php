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
use RunOpenCode\Sax\Handler\AbstractStackedSaxHandler;

class SampleStackedXmlHandler extends AbstractStackedSaxHandler
{
    /**
     * @var array<int, mixed>
     */
    protected array $output;

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
        // noop
    }

    /**
     * {@inheritdoc}
     */
    protected function onElementData(\XMLParser $parser, string $data): void
    {
        if (\trim($data)) {
            $this->output[] = $this->getCurrentElementName();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function onDocumentEnd(\XMLParser $parser, StreamInterface $stream): void
    {
        // noop
    }

    /**
     * {@inheritdoc}
     */
    protected function onParseError(string $message, int $code, int $lineno): void
    {
        // noop
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
