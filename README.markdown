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
    
API of this library is frozen and stable. Additional documentation is yet to come.    

