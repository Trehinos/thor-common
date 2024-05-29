<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Thor\Common\Debug\ResultState;
use Thor\Common\Debug\RetryResolver;
final class RetryResolverTest extends TestCase
{

    public function testRetry()
    {
        $retry = new RetryResolver(10);
        $nb    = 0;
        $retry->resolve(
            function () use (&$nb): ResultState {
                $nb = random_int(0, 3);
                return $nb === 1 ? ResultState::SUCCESS : ResultState::ERROR;
            }
        );
        $this->assertEquals(1, $nb);
    }

}
