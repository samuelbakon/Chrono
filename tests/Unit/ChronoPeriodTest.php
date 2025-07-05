<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SamyAsm\Chrono\Chrono;
use SamyAsm\Chrono\Traits\ChronoPeriodTrait;

#[CoversClass(ChronoPeriodTrait::class)]
class ChronoPeriodTest extends TestCase
{
    public function testGetIntervalOfToday(): void
    {
        $interval = Chrono::getTodayInterval();

        // Check that we have both start and end keys
        $this->assertArrayHasKey('start', $interval);
        $this->assertArrayHasKey('end', $interval);

        // Start should be today at 00:00:00
        $this->assertEquals('00:00:00', $interval['start']->format('H:i:s'));

        // End should be today at 23:59:59
        $this->assertEquals('23:59:59', $interval['end']->format('H:i:s'));

        // Both should be on the same day
        $this->assertEquals(
            $interval['start']->format('Y-m-d'),
            $interval['end']->format('Y-m-d')
        );
    }

    public function testAdjustFilterInterval(): void
    {
        // Test with both dates provided
        $start = new \DateTime('2023-06-01');
        $end = new \DateTime('2023-06-15');

        $result = Chrono::adjustInterval($start, $end);
        $this->assertEquals('2023-06-01', $result['start']->format('Y-m-d'));
        $this->assertEquals('2023-06-15', $result['end']->format('Y-m-d'));

        // Test with dates in wrong order (should be swapped)
        $result = Chrono::adjustInterval($end, $start);
        $this->assertEquals('2023-06-01', $result['start']->format('Y-m-d'));
        $this->assertEquals('2023-06-15', $result['end']->format('Y-m-d'));

        // Test with null start date (should default to 1 month before end date)
        $result = Chrono::adjustInterval(null, $end);
        $expectedStart = clone $end;
        $expectedStart->modify('-1 month');
        $this->assertEquals($expectedStart->format('Y-m-d'), $result['start']->format('Y-m-d'));
        $this->assertEquals('2023-06-15', $result['end']->format('Y-m-d'));

        // Test with both dates null (should use current date for end and 1 month before for start)
        $result = Chrono::adjustInterval();
        $this->assertInstanceOf(\DateTime::class, $result['start']);
        $this->assertInstanceOf(\DateTime::class, $result['end']);

        $expectedEnd = new \DateTime();
        $expectedStart = (clone $expectedEnd)->modify('-1 month');

        $this->assertEquals($expectedStart->format('Y-m-d'), $result['start']->format('Y-m-d'));
        $this->assertEquals($expectedEnd->format('Y-m-d'), $result['end']->format('Y-m-d'));
    }

    public function testGetDatesFromRange(): void
    {
        $start = '2023-06-01';
        $end = '2023-06-03';

        $dates = Chrono::getDateRange($start, $end);

        $this->assertCount(3, $dates);
        $this->assertEquals('2023-06-01', $dates[0]);
        $this->assertEquals('2023-06-02', $dates[1]);
        $this->assertEquals('2023-06-03', $dates[2]);

        // Test with custom format
        $dates = Chrono::getDateRange($start, $end, 'm/d/Y');
        $this->assertEquals('06/01/2023', $dates[0]);
    }

    public function testGetDayDatesInPeriod(): void
    {
        $start = new \DateTime('2023-06-01');
        $end = new \DateTime('2023-06-03');

        $dates = Chrono::getDaysInPeriod($start, $end);
        
        $this->assertCount(3, $dates);
        $this->assertEquals('2023-06-01', $dates[0]->format('Y-m-d'));
        $this->assertEquals('2023-06-02', $dates[1]->format('Y-m-d'));
        $this->assertEquals('2023-06-03', $dates[2]->format('Y-m-d'));
    }
}
