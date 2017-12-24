<?php
/*
 * This file is part of the runopencode/sax, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace StreamAdapter;

/**
 * Class StringAdapterTest
 *
 * @package RunOpenCode\Sax\Test\StreamAdapter
 */
use PHPUnit\Framework\TestCase;
use RunOpenCode\Sax\StreamAdapter\StringAdapter;

class StringAdapterTest extends TestCase
{
    /**
     * @test
     */
    public function itReadsStream()
    {
        $adapter = new StringAdapter();

        $xmlCode = '<?xml version="1.0" encoding="UTF-8"?><root><elem>value</elem></root>';

        $this->assertTrue($adapter->supports($xmlCode), 'Should support XML code as string.');
        $this->assertInstanceOf('Psr\\Http\\Message\\StreamInterface', $stream = $adapter->convert($xmlCode), 'Should provide us with StreamInterface wrapper.');

        $stream->close();
    }
}
