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

use PHPUnit\Framework\TestCase;
use RunOpenCode\Sax\StreamAdapter\DomDocumentAdapter;

/**
 * Class DomDocumentAdapterTest
 *
 * @package RunOpenCode\Sax\Test\StreamAdapter
 */
class DomDocumentAdapterTest extends TestCase
{
    /**
     * @test
     */
    public function itReadsStream()
    {
        $adapter = new DomDocumentAdapter();

        $document = new \DOMDocument();
        $document->load(__DIR__ . '/../Fixtures/sample.xml');

        $this->assertTrue($adapter->supports($document), 'Should support \DOMDocument');
        $this->assertInstanceOf('Psr\\Http\\Message\\StreamInterface', $stream = $adapter->convert($document), 'Should provide us with StreamInterface wrapper.');

        $stream->close();
    }

    /**
     * @test
     * @expectedException \RunOpenCode\Sax\Exception\StreamAdapterException
     */
    public function itThrowsExceptionWhenStreamHandlerCanNotBeAcquired()
    {
        $adapter = new DomDocumentAdapter('GuzzleHttp\\Psr7\\Stream', ['stream' => null ]);

        $document = new \DOMDocument();
        $document->load(__DIR__ . '/../Fixtures/sample.xml');

        $adapter->convert($document);
    }

    /**
     * @test
     * @expectedException \RunOpenCode\Sax\Exception\StreamAdapterException
     */
    public function itThrowsExceptionWhenStreamHandlerCanNotBeRewinded()
    {
        $adapter = new DomDocumentAdapter('GuzzleHttp\\Psr7\\Stream', ['stream' => 'php://stdin' ]);

        $document = new \DOMDocument();
        $document->load(__DIR__ . '/../Fixtures/sample.xml');

        $adapter->convert($document);
    }
}
