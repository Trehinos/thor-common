<?php

namespace Thor\Common\Debug;
class RetryResolver
{

    public function __construct(private readonly int $tries = 3, private readonly int $delay = 1)
    {
    }

    public function resolve($function, ...$arguments): ResultState
    {
        $index = 0;
        $result = ResultState::ERROR;
        while (($index < $this->tries && ($result = $function(...$arguments)) !== ResultState::SUCCESS)) {
            sleep($this->delay);
            $index++;
        }
        return $result;
    }

}
