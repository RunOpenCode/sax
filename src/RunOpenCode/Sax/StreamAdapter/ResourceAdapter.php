<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Sax\StreamAdapter;

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
    /**
     * @var string
     */
    private $streamClass;

    /**
     * ResourceAdapter constructor.
     *
     * @param string $streamClass FQCN of StreamInterface implementation, GuzzleHttp\Psr7\Stream is used by default.
     */
    public function __construct($streamClass = 'GuzzleHttp\\Psr7\\Stream')
    {
        $this->streamClass = $streamClass;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($xmlDocument)
    {
        return is_resource($xmlDocument) && get_resource_type($xmlDocument) === 'stream';
    }

    /**
     * {@inheritdoc}
     */
    public function convert($xmlDocument)
    {
        /**
         * @var resource $xmlDocument
         */
        if (class_exists($this->streamClass)) {
            return new $this->streamClass($xmlDocument);
        }

        throw new \RuntimeException(sprintf('Provided StreamInterface implementation "%s" is not available on this system.', $this->streamClass));
    }
}
