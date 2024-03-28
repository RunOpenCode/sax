<?php

declare(strict_types=1);

namespace RunOpenCode\Sax\Test\Handler;

use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use RunOpenCode\Sax\Test\Fixtures\CallbackXmlHandler;

final class CallbackHandlerTest extends TestCase
{
    public function testParse(): void
    {
        $handler = new CallbackXmlHandler();

        /**
         * @var resource $stream
         */
        $stream = \fopen(__DIR__ . '/../Fixtures/sample_callback.xml', 'rb');

        $buffer = [];

        $handler->parse(new Stream($stream), function (array $item) use (&$buffer) {
            $buffer[] = $item;
        });

        $this->assertEquals($this->getExpected(), $buffer);
    }

    public function testItRewindsStream(): void
    {
        $handler = new CallbackXmlHandler();

        /**
         * @var resource $stream
         */
        $stream = \fopen(__DIR__ . '/../Fixtures/sample_callback.xml', 'rb');

        $stream = new Stream($stream);

        while (!$stream->eof()) {
            $stream->read(1024);
        }

        $this->assertTrue($stream->eof());


        $buffer = [];

        $handler->parse($stream, function ($object) use (&$buffer) {
            $buffer[] = $object;
        });

        $this->assertEquals($this->getExpected(), $buffer);
    }

    public function testBroken(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Parser error "mismatched tag", lineno: 10');

        $handler = new CallbackXmlHandler();

        /**
         * @var resource $resource
         */
        $resource = \fopen(__DIR__ . '/../Fixtures/broken.xml', 'rb');

        $handler->parse(new Stream($resource));
    }

    /**
     * @return array<array{name: string, id:int}>
     */
    private static function getExpected(): array
    {
        return [
            [
                'name' => 'foo',
                'id' => 1,
            ],
            [
                'name' => 'bar',
                'id' => 2,
            ],
            [
                'name' => 'baz',
                'id' => 3,
            ],
        ];
    }
}
