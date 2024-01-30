<?php

namespace Thor\Common\Debug;

final class Debug
{
    public static function dump(mixed $var): void
    {
        dump($var);
    }

    public static function message(string $message, LogLevel $level = LogLevel::DEBUG): void
    {
        Logger::write($message, $level);
    }

    public static function print(string $message, LogLevel $level = LogLevel::DEBUG): void
    {
        Logger::write($message, $level, print: true);
    }

    public static function exception(\Throwable $t): void
    {
        Logger::logThrowable($t);
    }

    /**
     * Catch any Throwable thrown in $f and log it with Debug::exception().
     * Returns false if an error had been thrown, true otherwise.
     *
     * @param callable $f
     * @param mixed    ...$args
     *
     * @return bool
     */
    public static function tryOrLog(callable $f, mixed ...$args): bool
    {
        try {
            $f(...$args);
        } catch (\Throwable $t) {
            Debug::exception($t);
            return false;
        }
        return true;
    }

}
