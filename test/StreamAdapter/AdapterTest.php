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
use RunOpenCode\Sax\StreamAdapter\ResourceAdapter;
use RunOpenCode\Sax\StreamAdapter\SimpleXmlAdapter;

class AdapterTest extends TestCase
{
    /**
     * @test
     */
    public function resource()
    {
        $adapter = new ResourceAdapter();

        $resource = fopen(__DIR__ . '/../Fixtures/sample.xml', 'rb');

        $this->assertTrue($adapter->supports($resource), 'Should support resource');
        $this->assertInstanceOf('Psr\\Http\\Message\\StreamInterface', $stream = $adapter->convert($resource), 'Should provide us with StreamInterface wrapper.');

        $stream->close();
    }

    /**
     * @test
     */
    public function domDocument()
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
     */
    public function simpeXml()
    {
        $adapter = new SimpleXmlAdapter();

        $simpleXmlElement = new \SimpleXMLElement(file_get_contents(__DIR__ . '/../Fixtures/sample.xml'));

        $this->assertTrue($adapter->supports($simpleXmlElement), 'Should support \SimpleXMLElement');
        $this->assertInstanceOf('Psr\\Http\\Message\\StreamInterface', $stream = $adapter->convert($simpleXmlElement), 'Should provide us with StreamInterface wrapper.');

        $stream->close();
    }

    /**
     * @test
     */
    public function noOverlap()
    {
        $resourceAdapter = new ResourceAdapter();
        $domDocumentAdapter = new DomDocumentAdapter();
        $simpleXmlAdapter = new SimpleXmlAdapter();

        $resource = fopen(__DIR__ . '/../Fixtures/sample.xml', 'rb');

        $domDocument = new \DOMDocument();
        $domDocument->load(__DIR__ . '/../Fixtures/sample.xml');

        $simpleXmlElement = new \SimpleXMLElement(file_get_contents(__DIR__ . '/../Fixtures/sample.xml'));

        $this->assertTrue($resourceAdapter->supports($resource));
        $this->assertFalse($resourceAdapter->supports($domDocument));
        $this->assertFalse($resourceAdapter->supports($simpleXmlElement));

        $this->assertFalse($domDocumentAdapter->supports($resource));
        $this->assertTrue($domDocumentAdapter->supports($domDocument));
        $this->assertFalse($domDocumentAdapter->supports($simpleXmlElement));

        $this->assertFalse($simpleXmlAdapter->supports($resource));
        $this->assertFalse($simpleXmlAdapter->supports($domDocument));
        $this->assertTrue($simpleXmlAdapter->supports($simpleXmlElement));
    }
}
