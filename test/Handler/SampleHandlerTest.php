<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Sax\Test\Handler;

use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use RunOpenCode\Sax\Test\Fixtures\SampleXmlHandler;

class SampleHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function parse()
    {
        $handler = new SampleXmlHandler();

        $result = null;

        $handler->parse(new Stream(fopen(__DIR__ . '/../Fixtures/sample.xml', 'r+b')), function($output) use (&$result) {
            $result = $output;
        });

        $this->assertSame(include_once __DIR__ . '/../Fixtures/sample_output.php', $result);
    }

    /**
     * @test
     *
     * @expectedException \RunOpenCode\Sax\Exception\ParseException
     * @expectedExceptionMessage Unable to parse provided document stream
     */
    public function broken()
    {
        $handler = new SampleXmlHandler();
        $handler->parse(new Stream(fopen(__DIR__ . '/../Fixtures/broken.xml', 'rb+')));
    }
}
