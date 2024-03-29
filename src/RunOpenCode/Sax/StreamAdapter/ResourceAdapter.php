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

/**
 * Class ResourceAdapter
 *
 * PHP resource to stream adapter.
 *
 * @package RunOpenCode\Sax\Contract
 */
class ResourceAdapter implements StreamAdapterInterface
{
    private string $streamClass;

    /**
     * @param string $streamClass FQCN of StreamInterface implementation, GuzzleHttp\Psr7\Stream is used by default.
     */
    public function __construct(string $streamClass = Stream::class)
    {
        $this->streamClass = $streamClass;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(mixed $xmlDocument): bool
    {
        return \is_resource($xmlDocument) && \get_resource_type($xmlDocument) === 'stream';
    }

    /**
     * {@inheritdoc}
     */
    public function convert(mixed $xmlDocument): StreamInterface
    {
        $object = new $this->streamClass($xmlDocument);

        \assert($object instanceof StreamInterface);

        return $object;
    }
}
