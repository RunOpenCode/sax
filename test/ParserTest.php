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
use RunOpenCode\Sax\Exception\RuntimeException;
use RunOpenCode\Sax\SaxParser;
use RunOpenCode\Sax\StreamAdapter\DomDocumentAdapter;
use RunOpenCode\Sax\StreamAdapter\ResourceAdapter;
use RunOpenCode\Sax\StreamAdapter\SimpleXmlAdapter;
use RunOpenCode\Sax\Test\Fixtures\SampleXmlHandler;

final class ParserTest extends TestCase
{
    public function testIntegration(): void
    {
        $resourceAdapter = new ResourceAdapter();
        $domDocumentAdapter = new DomDocumentAdapter();
        $simpleXmlAdapter = new SimpleXmlAdapter();

        $parser = new SaxParser();

        $parser
            ->addStreamAdapter($resourceAdapter)
            ->addStreamAdapter($domDocumentAdapter)
            ->addStreamAdapter($simpleXmlAdapter);

        /**
         * @var string $content
         */
        $content = \file_get_contents(__DIR__ . '/Fixtures/sample.xml');

        $document = new \SimpleXMLElement($content);

        $result = $parser->parse(new SampleXmlHandler(), $document);

        $this->assertTrue(\is_array($result), 'Parsing should be executed.');
    }

    public function testFactory(): void
    {
        /**
         * @var string $content
         */
        $content = \file_get_contents(__DIR__ . '/Fixtures/sample.xml');

        $document = new \SimpleXMLElement($content);

        $result = SaxParser::factory()->parse(new SampleXmlHandler(), $document);

        $this->assertTrue(\is_array($result), 'Parsing should be executed.');
    }

    public function testNotSupported(): void
    {
        $this->expectException(RuntimeException::class);

        $resourceAdapter = new ResourceAdapter();
        $domDocumentAdapter = new DomDocumentAdapter();
        $simpleXmlAdapter = new SimpleXmlAdapter();

        $parser = new SaxParser();

        $parser
            ->addStreamAdapter($resourceAdapter)
            ->addStreamAdapter($domDocumentAdapter)
            ->addStreamAdapter($simpleXmlAdapter);

        $document = new class {
        };

        $parser->parse(new SampleXmlHandler(), $document);
    }
}
