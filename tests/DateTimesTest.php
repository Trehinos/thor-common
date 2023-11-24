<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Thor\Common\Types\DateTimes;

final class DateTimesTest extends TestCase
{
    function testTranslate(): void
    {
        $now = new \DateTime();
        $from = $now->format('Y-m-d H:i:s');
        $to = $now->format('YmdHis');

        $this->assertSame($to, DateTimes::translate($from, 'Y-m-d H:i:s', 'YmdHis'));
    }
    function testRelativeDateTime(): void
    {
        $dt1 = \DateTime::createFromFormat('Ymd His', '20231101 080000');
        $dt2 = \DateTime::createFromFormat('Ymd His', '20231102 040000');
        $dt3 = \DateTime::createFromFormat('Ymd His', '20231031 040000');
        $now = \DateTime::createFromFormat('Ymd His', '20231102 080000');

        $this->assertSame('Yesterday 08:00', DateTimes::getRelativeDateTime($dt1, relativeTo: $now));
        $this->assertSame('04:00', DateTimes::getRelativeDateTime($dt2, relativeTo: $now));
        $this->assertSame('2023-10-31', DateTimes::getRelativeDateTime($dt3, relativeTo: $now));
    }

    function testInstance(): void
    {
        $datetimes = DateTimes::get();
        $this->assertSame(date('YmdHis'), $datetimes->now()->format('YmdHis'));
    }

}
