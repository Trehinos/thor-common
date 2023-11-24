<?php

namespace Tests;

use Thor\Common\Types\Strings;
use PHPUnit\Framework\TestCase;

final class StringsTest extends TestCase
{

    function testSplit(): void
    {
        $toSplit = "value1:value2:value3:value4";
        $head    = '';

        $tail = Strings::split($toSplit, ':', $head);
        $this->assertSame('value1', $head);
        $tail = Strings::split($tail, ':', $head);
        $this->assertSame('value2', $head);
        $tail = Strings::split($tail, ':', $head);
        $this->assertSame('value3', $head);
        $tail = Strings::split($tail, ':', $head);
        $this->assertSame('value4', $head);
        $this->assertSame('', $tail);
    }

    function testToken(): void
    {
        $toTokenize = "value1:value2:value3:value4";
        $tail       = '';

        $head = Strings::token($toTokenize, ':', $tail);
        $this->assertSame('value1', $head);
        $head = Strings::token($tail, ':', $tail);
        $this->assertSame('value2', $head);
        $head = Strings::token($tail, ':', $tail);
        $this->assertSame('value3', $head);
        $head = Strings::token($tail, ':', $tail);
        $this->assertSame('value4', $head);
        $this->assertSame('', $tail);
    }

    function testTrimOrPad(): void
    {
        $toWorkOn = "   1234  ";

        $this->assertSame('1234', Strings::trimOrPad($toWorkOn, 4));
        $this->assertSame('  1234', Strings::trimOrPad($toWorkOn, 6));
    }

    function testLeft(): void
    {
        $this->assertSame('ab', Strings::left('abc', 2));
    }
    function testRight(): void
    {
        $this->assertSame('bc', Strings::right('abc', 2));
    }

    function testInterpolate(): void
    {
        $this->assertSame('Hello world', Strings::interpolate('Hello {token}', ['token' => 'world']));
    }

    function testPrefix(): void
    {
        $this->assertSame('p-fix', Strings::prefix('p-', 'fix'));
        $this->assertSame('', Strings::prefix('p-', ''));
    }

    function testSuffix(): void
    {
        $this->assertSame('fix-s', Strings::prefix('fix', '-s'));
        $this->assertSame('', Strings::suffix('', '-s'));
    }

}
