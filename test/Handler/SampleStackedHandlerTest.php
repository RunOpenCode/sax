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
    public function testParse(): void
    {
        $handler = new SampleStackedXmlHandler();

        /**
         * @var resource $stream
         */
        $stream = fopen(__DIR__ . '/../Fixtures/sample.xml', 'r+b');
        $result = $handler->parse(new Stream($stream));

        $this->assertSame([
            1,
            2,
            3,
            'COPYRIGHT',
            2,
            3,
            'NO',
            2,
            3,
            'DATE',
            2,
            3,
            'TYPE',
            2,
            1,
            2,
            3,
            'CODE',
            2,
            3,
            'COUNTRY',
            2,
            3,
            'CURRENCY',
            2,
            3,
            'UNIT',
            2,
            3,
            'MIDDLE_RATE',
            2,
            1,
            0,
        ], $result);
    }
}
