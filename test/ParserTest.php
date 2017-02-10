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
use RunOpenCode\Sax\SaxParser;
use RunOpenCode\Sax\StreamAdapter\DomDocumentAdapter;
use RunOpenCode\Sax\StreamAdapter\ResourceAdapter;
use RunOpenCode\Sax\StreamAdapter\SimpleXmlAdapter;
use RunOpenCode\Sax\Test\Fixtures\SampleXmlHandler;

class ParserTest extends TestCase
{
    /**
     * @test
     */
    public function integrationTest()
    {
        $resourceAdapter = new ResourceAdapter();
        $domDocumentAdapter = new DomDocumentAdapter();
        $simpleXmlAdapter = new SimpleXmlAdapter();

        $parser = new SaxParser();

        $parser
            ->addStreamAdapter($resourceAdapter)
            ->addStreamAdapter($domDocumentAdapter)
            ->addStreamAdapter($simpleXmlAdapter);

        $document = new \SimpleXMLElement(file_get_contents(__DIR__ . '/Fixtures/sample.xml'));

        $parser->parse(new SampleXmlHandler(), $document, \Closure::bind(function() {
            $this->assertTrue(true, 'Parsing should be executed.');
        }, $this));
    }

    /**
     * @test
     */
    public function factoryTest()
    {
        $document = new \SimpleXMLElement(file_get_contents(__DIR__ . '/Fixtures/sample.xml'));

        SaxParser::factory()->parse(new SampleXmlHandler(), $document, \Closure::bind(function() {
            $this->assertTrue(true, 'Parsing should be executed.');
        }, $this));
    }
}
