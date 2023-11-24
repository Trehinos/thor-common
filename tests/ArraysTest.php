<?php

namespace Tests;

use Thor\Common\Types\Arrays;
use PHPUnit\Framework\TestCase;

final class ArraysTest extends TestCase
{
    function testTurnOver(): void {
        $from = [['key' => 'value'], ['key' => 'value2']];
        $to = ['key' => ['value', 'value2']];

        $this->assertSame($to, Arrays::turnOver($from));
    }
}
