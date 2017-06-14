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
use RunOpenCode\Sax\StreamAdapter\ResourceAdapter;

/**
 * Class ResourceAdapterTest
 *
 * @package RunOpenCode\Sax\Test\StreamAdapter
 */
class ResourceAdapterTest extends TestCase
{
    /**
     * @test
     */
    public function itReadsStream()
    {
        $adapter = new ResourceAdapter();

        $resource = fopen(__DIR__ . '/../Fixtures/sample.xml', 'rb');

        $this->assertTrue($adapter->supports($resource), 'Should support resource');
        $this->assertInstanceOf('Psr\\Http\\Message\\StreamInterface', $stream = $adapter->convert($resource), 'Should provide us with StreamInterface wrapper.');

        $stream->close();
    }
}
