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
    public function itDoesNotOverlapSupportedSources()
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
