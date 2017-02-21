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
use RunOpenCode\Sax\Test\Fixtures\SampleStackedXmlHandler;

class SampleStackedHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function parse()
    {
        $handler = new SampleStackedXmlHandler();

        $result = $handler->parse(new Stream(fopen(__DIR__ . '/../Fixtures/sample.xml', 'r+b')));

        $this->assertSame([
            'COPYRIGHT',
            'NO',
            'DATE',
            'TYPE',
            'CODE',
            'COUNTRY',
            'CURRENCY',
            'UNIT',
            'MIDDLE_RATE'
        ], $result);
    }
}
