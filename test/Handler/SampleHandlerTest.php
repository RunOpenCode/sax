<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RunOpenCode\Sax\Test\Handler;

use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use RunOpenCode\Sax\Test\Fixtures\SampleXmlHandler;

final class SampleHandlerTest extends TestCase
{
    public function testParse(): void
    {
        $handler = new SampleXmlHandler();

        /**
         * @var resource $stream
         */
        $stream = \fopen(__DIR__ . '/../Fixtures/sample.xml', 'r+b');

        $result = $handler->parse(new Stream($stream));

        $this->assertSame(require __DIR__ . '/../Fixtures/sample_output.php', $result);
    }

    public function testItRewindsStream(): void
    {
        $handler = new SampleXmlHandler();

        /**
         * @var resource $stream
         */
        $stream = \fopen(__DIR__ . '/../Fixtures/sample.xml', 'r+b');

        $stream = new Stream($stream);

        while (!$stream->eof()) {
            $stream->read(1024);
        }

        $this->assertTrue($stream->eof());

        $result = $handler->parse($stream);
        $this->assertSame(require __DIR__ . '/../Fixtures/sample_output.php', $result);
    }

    public function testBroken(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Parser error "mismatched tag", lineno: 10');

        $handler = new SampleXmlHandler();

        /**
         * @var resource $resource
         */
        $resource = \fopen(__DIR__ . '/../Fixtures/broken.xml', 'rb');

        $handler->parse(new Stream($resource));
    }
}
