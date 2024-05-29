<?php

namespace Tests;

use Thor\Common\Types\Result;
use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase
{

    private function successResult(): Result
    {
        return Result::ok("Success !");
    }

    private function failedResult(): Result
    {
        return Result::error("Fail !");
    }


    function testOnSuccess(): void
    {
        $success = $this->successResult()->unwrap();
        $this->assertEquals("Success !", $success);

        $this->successResult()->then(
            fn($value) => $this->assertEquals("Success !", $value)
        );
    }

    function testOnFailure(): void
    {
        $result = $this->failedResult();

        $this->expectException(\RuntimeException::class);
        $result->unwrap();

        $result->unwrapOrElse(
            fn($value) => $this->assertEquals("Fail !", $value)
        );
    }


}
