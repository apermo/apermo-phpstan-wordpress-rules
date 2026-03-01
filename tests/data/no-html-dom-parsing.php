<?php

declare(strict_types=1);

// Should be flagged — DOMDocument::loadHTML
$dom = new DOMDocument();
$dom->loadHTML('<p>Hello</p>');

// Should be flagged — DOMDocument::loadHTMLFile
$dom2 = new DOMDocument();
$dom2->loadHTMLFile('/path/to/file.html');

// Should be flagged — Masterminds\HTML5 instantiation
$html5 = new Masterminds\HTML5();

// Should be flagged — Tidy::parseString
$tidy = new Tidy();
$tidy->parseString('<p>Hello</p>');

// Should be flagged — tidy_parse_string
tidy_parse_string('<p>Hello</p>');

// Should be flagged — tidy_parse_file
tidy_parse_file('/path/to/file.html');

// Should NOT be flagged — DOMDocument::loadXML (XML is fine)
$xml_dom = new DOMDocument();
$xml_dom->loadXML('<root/>');

// Should NOT be flagged — DOMDocument::load (XML file)
$xml_dom2 = new DOMDocument();
$xml_dom2->load('/path/to/file.xml');

// Should NOT be flagged — DOMDocument creation without loadHTML
$xml_dom3 = new DOMDocument('1.0', 'UTF-8');
$root = $xml_dom3->createElement('root');

// Should NOT be flagged — WP_HTML_Tag_Processor
$processor = new WP_HTML_Tag_Processor('<p>Hello</p>');

// Should NOT be flagged — simplexml_load_string
$xml = simplexml_load_string('<root/>');
