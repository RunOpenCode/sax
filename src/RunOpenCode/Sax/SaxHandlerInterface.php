<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Sax;

use Psr\Http\Message\StreamInterface;

interface SaxHandlerInterface
{
    /**
     * Parse XML content and get result.
     *
     * @param StreamInterface $stream Streamed XML document.
     * @param callable|null $result Callable to execute when parsing is completed.
     *
     * @return mixed Parsing result.
     */
    public function parse(StreamInterface $stream, callable $result = null);
}
