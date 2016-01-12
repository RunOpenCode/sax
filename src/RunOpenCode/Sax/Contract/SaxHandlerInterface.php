<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Sax\Contract;

use Psr\Http\Message\StreamInterface;

/**
 * Interface SaxHandlerInterface
 *
 * Sax handler interface.
 *
 * @package RunOpenCode\Sax
 */
interface SaxHandlerInterface
{
    /**
     * Parse XML content and get result.
     *
     * @param StreamInterface $stream Streamed XML document.
     * @param callable|null $onResult Callable to execute when parsing is completed and parsing result can be provided.
     */
    public function parse(StreamInterface $stream, callable $onResult = null);
}
