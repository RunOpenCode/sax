<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StreamAdapter;

/**
 * Class StringAdapterTest
 *
 * @package RunOpenCode\Sax\Test\StreamAdapter
 */

use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use RunOpenCode\Sax\Exception\StreamAdapterException;
use RunOpenCode\Sax\StreamAdapter\StringAdapter;

final class StringAdapterTest extends TestCase
{
    public function testItReadsStream(): void
    {
        $adapter = new StringAdapter();

        $xmlCode = '<?xml version="1.0" encoding="UTF-8"?><root><elem>value</elem></root>';

        $this->assertTrue($adapter->supports($xmlCode), 'Should support XML code as string.');
        $this->assertInstanceOf(StreamInterface::class, $stream = $adapter->convert($xmlCode), 'Should provide us with StreamInterface wrapper.');

        $stream->close();
    }

    public function testItThrowsExceptionWhenStreamIsNotProvided(): void
    {
        $this->expectException(StreamAdapterException::class);
        $this->expectExceptionMessage('Stream is not provided.');

        $adapter = new StringAdapter(Stream::class, ['stream' => null]);

        $xmlCode = '<?xml version="1.0" encoding="UTF-8"?><root><elem>value</elem></root>';

        $adapter->convert($xmlCode);
    }

    public function testItThrowsExceptionWhenStreamHandlerCanNotBeAcquired(): void
    {
        $this->expectException(StreamAdapterException::class);
        $this->expectExceptionMessage('Unable to acquire resource handler on "foo".');

        $adapter = new StringAdapter(Stream::class, ['stream' => 'foo']);

        $xmlCode = '<?xml version="1.0" encoding="UTF-8"?><root><elem>value</elem></root>';

        $adapter->convert($xmlCode);
    }

    public function testItThrowsExceptionWhenStreamHandlerCanNotBeRewinded(): void
    {
        $this->markTestIncomplete('We cannot prevent output buffer not to flush into CLI.');

        /** @phpstan-ignore-next-line */
        $this->expectException(StreamAdapterException::class);
        $this->expectExceptionMessage('Unable to to rewind stream.');

        $adapter = new StringAdapter(Stream::class, ['stream' => 'php://stdout']);

        $xmlCode = '<?xml version="1.0" encoding="UTF-8"?><root><elem>value</elem></root>';

        $adapter->convert($xmlCode);
    }
}
