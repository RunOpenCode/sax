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
use RunOpenCode\Sax\StreamAdapter\StringAdapter;

final class AdapterTest extends TestCase
{
    public function testItDoesNotOverlapSupportedSources(): void
    {
        $resourceAdapter = new ResourceAdapter();
        $domDocumentAdapter = new DomDocumentAdapter();
        $simpleXmlAdapter = new SimpleXmlAdapter();
        $stringAdapter = new StringAdapter();

        $resource = fopen(__DIR__ . '/../Fixtures/sample.xml', 'rb');

        $domDocument = new \DOMDocument();
        $domDocument->load(__DIR__ . '/../Fixtures/sample.xml');

        /**
         * @var string $content
         */
        $content = \file_get_contents(__DIR__ . '/../Fixtures/sample.xml');

        $simpleXmlElement = new \SimpleXMLElement($content);

        $xmlString = '<?xml version="1.0" encoding="UTF-8"?><root><elem>value</elem></root>';

        $this->assertTrue($resourceAdapter->supports($resource));
        $this->assertFalse($resourceAdapter->supports($domDocument));
        $this->assertFalse($resourceAdapter->supports($simpleXmlElement));
        $this->assertFalse($resourceAdapter->supports($xmlString));

        $this->assertFalse($domDocumentAdapter->supports($resource));
        $this->assertTrue($domDocumentAdapter->supports($domDocument));
        $this->assertFalse($domDocumentAdapter->supports($simpleXmlElement));
        $this->assertFalse($domDocumentAdapter->supports($xmlString));

        $this->assertFalse($simpleXmlAdapter->supports($resource));
        $this->assertFalse($simpleXmlAdapter->supports($domDocument));
        $this->assertTrue($simpleXmlAdapter->supports($simpleXmlElement));
        $this->assertFalse($simpleXmlAdapter->supports($xmlString));

        $this->assertFalse($stringAdapter->supports($resource));
        $this->assertFalse($stringAdapter->supports($domDocument));
        $this->assertFalse($stringAdapter->supports($simpleXmlElement));
        $this->assertTrue($stringAdapter->supports($xmlString));
    }
}
