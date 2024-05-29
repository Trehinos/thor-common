<?php

namespace Thor\Common\Types;

use Throwable;
use RuntimeException;

final class Result
{

    private bool  $isOk  = false;
    private mixed $value = null;

    private function __construct()
    {
    }

    public static function ok(mixed $successValue): self
    {
        $result        = new self();
        $result->value = $successValue;
        $result->isOk  = true;
        return $result;
    }

    public static function error(mixed $failureValue): self
    {
        $result        = new self();
        $result->value = $failureValue;
        $result->isOk  = false;
        return $result;
    }

    public function isOk(): bool
    {
        return $this->isOk;
    }

    public function isError(): bool
    {
        return !$this->isOk();
    }

    public function match(callable $onSuccess, callable $onFailure): mixed
    {
        if ($this->isOk()) {
            return $onSuccess($this->value);
        } else {
            return $onFailure($this->value);
        }
    }

    public function unwrapOrElse(callable $onFailure): mixed
    {
        return $this->match(fn($success) => $success, $onFailure);
    }

    /**
     * @throws Throwable
     */
    public function unwrapOrThrow(Throwable $t): mixed
    {
        return $this->unwrapOrElse(fn($_) => throw $t);
    }

    public function unwrapOr(mixed $errorValue): mixed
    {
        return $this->unwrapOrElse(fn($_) => $errorValue);
    }

    /**
     * @throws Throwable
     */
    public function unwrap(): mixed
    {
        return $this->unwrapOrThrow(new RuntimeException("Unable to unwrap a failed result."));
    }

    public function then(callable $onSuccess): mixed
    {
        return $this->match(
            $onSuccess,
            fn($_) => null
        );
    }

}
