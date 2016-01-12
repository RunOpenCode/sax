<?php
/**
 * Class AbstractSaxHandler
 *
 * Sax handler prototype.
 *
 * @package RunOpenCode\Sax
 */
namespace RunOpenCode\Sax;

final class SaxParser
{
    private static $instance;

    private function __construct() { }

    public function parse(SaxHandlerInterface $handler, $document, callable $result = null)
    {
        return $handler->parse($this->getStream($document), $result);
    }

    private function getStream($document)
    {
        if (is_resource($document)) {
            return new Stream($document);
        }

        return new Stream(fopen($document, 'r'));
    }

    public static function get()
    {
        if (!self::$instance) {
            self::$instance = new SaxParser();
        }

        return self::$instance;
    }
}
