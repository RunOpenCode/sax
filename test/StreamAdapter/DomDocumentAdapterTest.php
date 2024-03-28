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
use RunOpenCode\Sax\StreamAdapter\DomDocumentAdapter;
use RunOpenCode\Sax\Exception\StreamAdapterException;

/**
 * Class DomDocumentAdapterTest
 *
 * @package RunOpenCode\Sax\Test\StreamAdapter
 */
class DomDocumentAdapterTest extends TestCase
{
    public function testItReadsStream(): void
    {
        $adapter = new DomDocumentAdapter();

        $document = new \DOMDocument();
        $document->load(__DIR__ . '/../Fixtures/sample.xml');

        $this->assertTrue($adapter->supports($document), 'Should support \DOMDocument');

        $this->assertInstanceOf('Psr\\Http\\Message\\StreamInterface', $stream = $adapter->convert($document), 'Should provide us with StreamInterface wrapper.');

        $stream->close();
    }

    public function testItThrowsExceptionWhenStreamIsNotProvided(): void
    {
        $this->expectException(StreamAdapterException::class);
        $this->expectExceptionMessage('Stream is not provided.');

        $adapter = new DomDocumentAdapter(Stream::class, ['stream' => null]);

        $document = new \DOMDocument();
        $document->load(__DIR__ . '/../Fixtures/sample.xml');

        $adapter->convert($document);
    }

    public function testItThrowsExceptionWhenStreamHandlerCanNotBeAcquired(): void
    {
        $this->expectException(StreamAdapterException::class);
        $this->expectExceptionMessage('Unable to acquire resource handler on "foo".');

        $adapter = new DomDocumentAdapter(Stream::class, ['stream' => 'foo']);

        $document = new \DOMDocument();
        $document->load(__DIR__ . '/../Fixtures/sample.xml');

        $adapter->convert($document);
    }

    public function testItThrowsExceptionWhenStreamHandlerCanNotBeRewinded(): void
    {
        $this->markTestIncomplete('We cannot prevent output buffer not to flush into CLI.');

        /** @phpstan-ignore-next-line */
        $this->expectException(StreamAdapterException::class);
        $this->expectExceptionMessage('Unable to to rewind stream.');

        $adapter = new DomDocumentAdapter(Stream::class, ['stream' => 'php://stdout']);

        $document = new \DOMDocument();
        $document->load(__DIR__ . '/../Fixtures/sample.xml');

        $adapter->convert($document);
    }
}
