<?php

namespace FlynSyntaxTests;

use FlynSyntax\Highlighter;
use PHPUnit\Framework\TestCase;

final class HighlighterTest extends TestCase
{
    public function testParsesLineRangesCorrectly(): void
    {
        $highlighter = new Highlighter();

        // None passed
        $expected = [];
        $actual = $highlighter->parseHighlightLines("");
        $this->assertEquals($expected, $actual);

        // A single line
        $expected = [4];
        $actual = $highlighter->parseHighlightLines("4");
        $this->assertEquals($expected, $actual);

        // A single range
        $expected = [4, 5, 6];
        $actual = $highlighter->parseHighlightLines("4-6");
        $this->assertEquals($expected, $actual);

        // Multiple single lines
        $expected = [4, 5, 6];
        $actual = $highlighter->parseHighlightLines("4,5,6");
        $this->assertEquals($expected, $actual);

        // Multiple single lines with spaces
        $expected = [4, 5, 7, 8, 10, 11, 12];
        $actual = $highlighter->parseHighlightLines("4,5, 7,8, 10, 11, 12");
        $this->assertEquals($expected, $actual);

        // Mixed lines and ranges
        $expected = [4, 5, 6, 8, 9, 10, 11, 12, 13];
        $actual = $highlighter->parseHighlightLines("4-6,8, 9, 10-13");
        $this->assertEquals($expected, $actual);

        // Invalid range - syntax error
        $expected = [4, 5, 6, 10, 11, 12];
        $actual = $highlighter->parseHighlightLines("4-6,, 9-, 10-12, 14-");
        $this->assertEquals($expected, $actual);

        // Invalid range - end < start
        $expected = [5, 6];
        $actual = $highlighter->parseHighlightLines("4-3, 5-6");
        $this->assertEquals($expected, $actual);

        // Invalid single lines
        $expected = [4, 7, 11];
        $actual = $highlighter->parseHighlightLines("4,7,,11");
        $this->assertEquals($expected, $actual);
    }

    public function testItGetsRelativeLines(): void
    {
        $highlighter = new Highlighter();

        // Single line starting at line 1
        $expected = [4];
        $actual = $highlighter->getRelativeLines([4], 1);
        $this->assertEquals($expected, $actual);

        // Multiple lines starting at line 1
        $expected = [4, 5, 7];
        $actual = $highlighter->getRelativeLines([4, 5, 7], 1);
        $this->assertEquals($expected, $actual);

        // Single line starting at line 3
        $expected = [2];
        $actual = $highlighter->getRelativeLines([4], 3);
        $this->assertEquals($expected, $actual);

        // Multiple lines starting at line 3
        $expected = [2, 3, 5];
        $actual = $highlighter->getRelativeLines([4, 5, 7], 3);
        $this->assertEquals($expected, $actual);
    }
}
