<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Sax\Test\Handler;

use GuzzleHttp\Psr7\Stream;
use RunOpenCode\Sax\Test\Fixtures\SampleXmlHandler;

class SampleHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function parse()
    {
        $handler = new SampleXmlHandler();

        $result = null;

        $handler->parse(new Stream(fopen(__DIR__ . '/../Fixtures/sample.xml', 'r+')), function($output) use (&$result) {
            $result = $output;

        });

        echo var_export($result, true);

        $this->assertSame(include_once __DIR__ . '/../Fixtures/sample_output.php', $result);
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Parser error "mismatched tag", lineno: 10
     */
    public function broken()
    {
        $handler = new SampleXmlHandler();
        $handler->parse(new Stream(fopen(__DIR__ . '/../Fixtures/broken.xml', 'r+')));
    }
}
