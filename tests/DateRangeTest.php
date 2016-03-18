<?php

namespace Imos\Invoice\Tests;

use DateTime;
use Imos\Invoice\DateRange;

class DateRangeTest extends \PHPUnit_Framework_TestCase
{
    public function testDateRange()
    {
        $start = new DateTime('2000-01-01');
        $end = new DateTime('2000-02-01');
        $range = new DateRange($start, $end);

        $this->assertEquals($start, $range->getStart());
        $this->assertEquals($end, $range->getEnd());

        $this->assertEquals($start->diff($end), $range->getInterval());
    }

    public function testCreate()
    {
        $range = DateRange::create('2000-01-01', '2000-02-01');

        $this->assertEquals(new DateTime('2000-01-01'), $range->getStart());
        $this->assertEquals(new DateTime('2000-02-01'), $range->getEnd());
    }

    /**
     * @expectedException \Exception
     */
    public function testNegative()
    {
        DateRange::create('2000-02-01', '2000-01-01');
    }
}
