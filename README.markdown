Java like SAX XML parsing
======

[![Packagist](https://img.shields.io/packagist/v/RunOpenCode/sax.svg)](https://packagist.org/packages/runopencode/sax)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/RunOpenCode/sax/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/RunOpenCode/sax/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/RunOpenCode/sax/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/RunOpenCode/sax/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/RunOpenCode/sax/badges/build.png?b=master)](https://scrutinizer-ci.com/g/RunOpenCode/sax/build-status/master)
[![Build Status](https://travis-ci.org/RunOpenCode/sax.svg?branch=master)](https://travis-ci.org/RunOpenCode/sax)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/663ee0ee-08c1-4ee2-9d5a-6889b06077be/big.png)](https://insight.sensiolabs.com/projects/663ee0ee-08c1-4ee2-9d5a-6889b06077be)

This library enables you to parse XML documents with SAX in Java style: instead of handling events by using these nasty
functions and callbacks (see official PHP documentation example [here](http://php.net/manual/en/example.xml-structure.php)),
you can just inherit provided abstract class `RunOpenCode\Sax\Handler\AbstractSaxHandler` and implement all of its abstract
methods.

Major benefit of using this library is clean, human-readable code.

Example:

    class MySaxHandler extends RunOpenCode\Sax\Handler\AbstractSaxHandler {
        // ... your implementation 
    }
    
    $parser = RunOpenCode\Sax\SaxParser::factory()->parse(new MySaxHandler(), $myXmlDocumentResource, function($result){
        // Your result callback
    });
    
    
List of methods which you ought to implement:
     
- `onDocumentStart`: executed when parsing started of XML document.
- `onElementStart`: executed when parser stumbled upon new XML tag.
- `onElementData`: executed when parser stumbled upon CDATA of some XML tag.
- `onElementEnd`: executed when parser stumbled upon closed already opened XML tag.
- `onDocumentEnd`: executed when parsing of XML document is done.
- `onParseError`: executed when parsing error is triggered. 
- `onResult`: executed at very end of parsing process where you can execute provided callable and provide callee with
parsing results. Form and API of result callable is up to you and your needs.

**Important notes**

- Due to underlying implementation of PHP XML parser, all tag names in relevant event calls are provided uppercase. Per example,
if you have tag `<tag></tag>`, in relevant event methods your check for tag name should be `if ($name === 'TAG')`. 
- Event `onParseError` is due to unrecoverable parsing error, however, it is up to you and your use case weather you are
going to trigger error continue with execution.
- Event `onElementData` will trigger even if you have blank spaces only between tags in XML document. 
 
# SaxParser and StreamAdapterInterface

`RunOpenCode\Sax\SaxParser` is provided as utility class which ought to ease up your usage of your SaxHandler implementation. SaxHandler
uses `Psr\Http\Message\StreamInterface` implementation as source of XML document for parsing, however, StreamAdapters
can help you to work with various XML document sources, such as:

- Resources (file resources or PHP native streams)
- DOMDocument
- SimpleXMLElement

If you need any other type of XML document source, you can provide it by implementing `RunOpenCode\Sax\Contract\StreamAdapterInterface`,
and you can register it to `RunOpenCode\Sax\SaxParser` instance via `SaxParser::addStreamAdapter()` method call.
 
When you invoke `SaxParser::parse()`, before parsing, source of provided XML document will be checked against available 
adapters and converted to `Psr\Http\Message\StreamInterface` implementation.

This library recommends [guzzlehttp/psr7](https://github.com/guzzle/psr7) and uses it as default `StreamInterface` implementation,
but you can use any other implementation that suits your need.

API of this library is frozen and stable.     

## Changelog

### February 10th, 2017.
- Dropped support for PHP 5.x
- Added PHPUnit 6.x as requirement
- Added lib exceptions