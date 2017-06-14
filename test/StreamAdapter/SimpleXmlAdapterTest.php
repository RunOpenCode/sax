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
use RunOpenCode\Sax\StreamAdapter\SimpleXmlAdapter;

/**
 * Class SimpleXmlAdapterTest
 *
 * @package RunOpenCode\Sax\Test\StreamAdapter
 */
class SimpleXmlAdapterTest extends TestCase
{
    /**
     * @test
     */
    public function itReadsStream()
    {
        $adapter = new SimpleXmlAdapter();

        $simpleXmlElement = new \SimpleXMLElement(file_get_contents(__DIR__ . '/../Fixtures/sample.xml'));

        $this->assertTrue($adapter->supports($simpleXmlElement), 'Should support \SimpleXMLElement');
        $this->assertInstanceOf('Psr\\Http\\Message\\StreamInterface', $stream = $adapter->convert($simpleXmlElement), 'Should provide us with StreamInterface wrapper.');

        $stream->close();
    }

    /**
     * @test
     * @expectedException \RunOpenCode\Sax\Exception\StreamAdapterException
     */
    public function itThrowsExceptionWhenStreamHandlerCanNotBeAcquired()
    {
        $adapter = new SimpleXmlAdapter('GuzzleHttp\\Psr7\\Stream', ['stream' => null ]);

        $simpleXmlElement = new \SimpleXMLElement(file_get_contents(__DIR__ . '/../Fixtures/sample.xml'));

        $adapter->convert($simpleXmlElement);
    }

    /**
     * @test
     * @expectedException \RunOpenCode\Sax\Exception\StreamAdapterException
     */
    public function itThrowsExceptionWhenStreamHandlerCanNotBeRewinded()
    {
        $adapter = new SimpleXmlAdapter('GuzzleHttp\\Psr7\\Stream', ['stream' => 'php://stdin' ]);

        $simpleXmlElement = new \SimpleXMLElement(file_get_contents(__DIR__ . '/../Fixtures/sample.xml'));

        $adapter->convert($simpleXmlElement);
    }
}
