<?php

namespace Thor\Common\Debug;

use Attribute;
use Throwable;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION | Attribute::IS_REPEATABLE)]
final class Throws
{

    /**
     * Indicates that a function or a method can throw an exception.
     *
     * @template T
     * @template-implements Throwable
     * @param class-string<T> $throwable
     */
    public function __construct(public readonly string $throwable) {
    }

}
