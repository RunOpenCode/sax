<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Sax\Contract;

use Psr\Http\Message\StreamInterface;

/**
 * Interface StreamAdapterInterface
 *
 * Converts XML document source to stream interface source.
 *
 * @package RunOpenCode\Sax\Contract
 */
interface StreamAdapterInterface
{
    /**
     * Check if stream adapter can convert XML document source to stream interface implementation.
     *
     * @param mixed $xmlDocument XML document source.
     * @return bool TRUE if source is supported.
     */
    public function supports($xmlDocument);

    /**
     * Convert XML document source to stream interface implementation.
     *
     * @param mixed $xmlDocument XML document source to convert.
     * @return StreamInterface XML document in stream.
     *
     * @throws \Exception If conversion is impossible.
     */
    public function convert($xmlDocument);
}
