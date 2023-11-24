<?php

namespace Tests;

use Thor\Common\Guid;
use PHPUnit\Framework\TestCase;

final class GuidTest extends TestCase
{

    function testGuidInstance(): void
    {
        $guid = new Guid();
        $this->assertInstanceOf(Guid::class, $guid);
    }

    function testGuidOutput(): void
    {
        $guid = new Guid();

        $this->assertTrue(16 === strlen($guid->get()));
        $this->assertTrue(32 === strlen($guid->getHex()));
        $this->assertNotSame("$guid", $guid->getHex());
    }


}
