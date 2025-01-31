<?php

namespace Thor\Common\Types;

use Exception;
use DatePeriod;
use DateInterval;
use DateTimeInterface;
use DateTimeImmutable;

/**
 * Provides some Date and Time utilities.
 *
 * @package          Thor/Tools
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class DateTimes implements ClockInterface
{

    private function __construct()
    {
    }

    /**
     * @param string $date
     * @param string $from
     * @param string $to
     *
     * @return string
     */
    public static function translate(string $date, string $from = 'Y-m-d', string $to = 'd/m/Y'): string
    {
        return DateTimeImmutable::createFromFormat($from, $date)->format($to);
    }

    /**
     * Returns a string from a DateTimeInterface, accordingly to relative time
     * between $relativeTo (default is now) and $date :
     * - `< 24h` and **today** : "H:i"
     * - `< 24h` and **yesterday** : "$yesterday H:i"
     * - `> 24h` : $dateFormat
     */
    public static function getRelativeDateTime(
        DateTimeInterface $date,
        string $dateFormat = 'Y-m-d',
        string $yesterday = 'Yesterday',
        DateTimeInterface $relativeTo = new DateTimeImmutable()
    ): string {
        $diff = $date->diff($relativeTo);
        if ($diff->format('%a%H%I%S') > 1000000) {
            return $date->format($dateFormat);
        }
        $prefix = '';
        if ($relativeTo->format('Ymd') !== $date->format('Ymd')) {
            $prefix = "$yesterday ";
        }
        return $prefix . $date->format('H:i');
    }

    /**
     * Returns a DatePeriod between $start and $end with $interval interval.
     *
     * Il $end is not specified, it will be the last day of $start's month.
     *
     * @throws Exception if $interval is not a valid interval string.
     */
    public static function period(
        DateTimeInterface $start,
        ?DateTimeInterface $end = null,
        string $interval = 'P1D'
    ): DatePeriod {
        if ($end === null) {
            $end = clone $start;
            $end = $end->modify('last day of this month');
        }
        return new DatePeriod($start, new DateInterval($interval), $end);
    }

    public static function get(): self
    {
        static $s = new self();
        return $s;
    }

    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
