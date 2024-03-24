<?php

declare(strict_types=1);

namespace RunOpenCode\Sax\Test\Handler;

use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use RunOpenCode\Sax\Test\Fixtures\CallbackStackedXmlHandler;

final class CallbackStackedHandlerTest extends TestCase
{
    public function testParse(): void
    {
        $handler = new CallbackStackedXmlHandler();

        /**
         * @var resource $stream
         */
        $stream = \fopen(__DIR__ . '/../Fixtures/sample_callback.xml', 'rb');

        $buffer = [];

        $stack = $handler->parse(new Stream($stream), function (array $item) use (&$buffer) {
            $buffer[] = $item;
        });

        $this->assertEquals($this->getExpectedItems(), $buffer);
        $this->assertEquals($this->getExpectedStack(), $stack);
    }

    /**
     * @return array<array{name: string, id:int}>
     */
    private static function getExpectedItems(): array
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

    /**
     * @return array<int|string>
     */
    private static function getExpectedStack(): array
    {
        return [
            1,
            2,
            "HEADER",
            1,
            2,
            3,
            4,
            "NAME",
            3,
            4,
            "ID",
            3,
            2,
            3,
            4,
            "NAME",
            3,
            4,
            "ID",
            3,
            2,
            3,
            4,
            "NAME",
            3,
            4,
            "ID",
            3,
            2,
            1,
            0
        ];
    }
}