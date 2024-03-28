<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RunOpenCode\Sax\Test\StreamAdapter;

use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use RunOpenCode\Sax\Exception\StreamAdapterException;
use RunOpenCode\Sax\StreamAdapter\SimpleXmlAdapter;

/**
 * Class SimpleXmlAdapterTest
 *
 * @package RunOpenCode\Sax\Test\StreamAdapter
 */
class SimpleXmlAdapterTest extends TestCase
{
    public function testItReadsStream(): void
    {
        $adapter = new SimpleXmlAdapter();

        /**
         * @var string $content
         */
        $content = \file_get_contents(__DIR__ . '/../Fixtures/sample.xml');

        $simpleXmlElement = new \SimpleXMLElement($content);

        $this->assertTrue($adapter->supports($simpleXmlElement), 'Should support \SimpleXMLElement');
        $this->assertInstanceOf(StreamInterface::class, $stream = $adapter->convert($simpleXmlElement), 'Should provide us with StreamInterface wrapper.');

        $stream->close();
    }

    public function testItThrowsExceptionWhenStreamIsNotProvided(): void
    {
        $this->expectException(StreamAdapterException::class);
        $this->expectExceptionMessage('Stream is not provided.');

        $adapter = new SimpleXmlAdapter(Stream::class, ['stream' => null]);

        /**
         * @var string $content
         */
        $content = \file_get_contents(__DIR__ . '/../Fixtures/sample.xml');

        $simpleXmlElement = new \SimpleXMLElement($content);

        $adapter->convert($simpleXmlElement);
    }

    public function testItThrowsExceptionWhenStreamHandlerCanNotBeAcquired(): void
    {
        $this->expectException(StreamAdapterException::class);
        $this->expectExceptionMessage('Unable to acquire resource handler on "foo".');

        $adapter = new SimpleXmlAdapter(Stream::class, ['stream' => 'foo']);

        /**
         * @var string $content
         */
        $content = \file_get_contents(__DIR__ . '/../Fixtures/sample.xml');

        $simpleXmlElement = new \SimpleXMLElement($content);

        $adapter->convert($simpleXmlElement);
    }

    public function testItThrowsExceptionWhenStreamHandlerCanNotBeRewinded(): void
    {
        $this->markTestIncomplete('We cannot prevent output buffer not to flush into CLI.');

        /** @phpstan-ignore-next-line */
        $this->expectException(StreamAdapterException::class);
        $this->expectExceptionMessage('Unable to to rewind stream.');

        $adapter = new SimpleXmlAdapter(Stream::class, ['stream' => 'php://stdout']);

        /**
         * @var string $content
         */
        $content = \file_get_contents(__DIR__ . '/../Fixtures/sample.xml');

        $simpleXmlElement = new \SimpleXMLElement($content);

        $adapter->convert($simpleXmlElement);
    }
}
