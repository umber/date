<?php

declare(strict_types=1);

namespace Umber\Date\Tests\Unit\Range;

use Umber\Date\Exception\Range\DateRangeInvalidException;
use Umber\Date\Range\DateRange;

use PHPUnit\Framework\TestCase;

use DateInterval;
use DateTimeImmutable;

/**
 * @group unit
 *
 * @covers \Umber\Date\Range\DateRange
 */
final class DateRangeTest extends TestCase
{
    /**
     * @test
     */
    public function canDetectNullStart(): void
    {
        $range = new DateRange(null, new DateTimeImmutable('2018-01-10'));

        self::assertNull($range->start());
        self::assertNotNull($range->finish());
        self::assertNull($range->interval());
    }

    /**
     * @test
     */
    public function canDetectNullFinish(): void
    {
        $range = new DateRange(new DateTimeImmutable('2018-01-10'), null);

        self::assertNotNull($range->start());
        self::assertNull($range->finish());
        self::assertNull($range->interval());
    }

    /**
     * @test
     */
    public function cannotHaveNullStartAndFinish(): void
    {
        self::expectException(DateRangeInvalidException::class);
        self::expectExceptionMessage(implode(' ', [
            'A date range cannot be constructed with null start and finish dates.',
            'Please provide at least one side of the date range.',
        ]));

        new DateRange(null, null);
    }

    /**
     * @test
     */
    public function canDetectOpenEndedNullStart(): void
    {
        $range = new DateRange(null, new DateTimeImmutable('2018-01-10'));

        self::assertTrue($range->hasOpenStart());
        self::assertTrue($range->isOpenEnded());
    }

    /**
     * @test
     */
    public function canDetectOpenEndedNullFinish(): void
    {
        $range = new DateRange(new DateTimeImmutable('2018-01-10'), null);

        self::assertTrue($range->hasOpenFinish());
        self::assertTrue($range->isOpenEnded());
    }

    /**
     * @test
     */
    public function canDetectInterval(): void
    {
        $range = new DateRange(
            new DateTimeImmutable('2018-01-10'),
            new DateTimeImmutable('2018-01-10'),
            new DateInterval('P1D')
        );

        self::assertNotNull($range->start());
        self::assertNotNull($range->finish());
        self::assertNotNull($range->interval());
    }

    /**
     * @test
     */
    public function canGetPeriodForRangeNotInclusive(): void
    {
        $range = new DateRange(
            new DateTimeImmutable('2018-01-10'),
            new DateTimeImmutable('2018-01-15'),
            new DateInterval('P1D')
        );

        $period = $range->period();
        self::assertEquals('2018-01-10', $period->getStartDate()->format('Y-m-d'));
        self::assertEquals('2018-01-15', $period->getEndDate()->format('Y-m-d'));

        self::assertCount(6, iterator_to_array($period));
    }

    /**
     * @test
     */
    public function canGetPeriodForRangeInclusive(): void
    {
        $range = new DateRange(
            new DateTimeImmutable('2018-01-10'),
            new DateTimeImmutable('2018-01-15'),
            new DateInterval('P1D')
        );

        $period = $range->period(false);
        self::assertEquals('2018-01-11', $period->getStartDate()->format('Y-m-d'));
        self::assertEquals('2018-01-14', $period->getEndDate()->format('Y-m-d'));

        self::assertCount(4, iterator_to_array($period));
    }

    /**
     * Data provider.
     *
     * @return mixed[][]
     */
    public function provideCanCheckDateBefore(): array
    {
        return [
            'before' => ['2018-01-09', false, true],
            'before-exact' => ['2018-01-10', false, false],
            'before-exact-inclusive' => ['2018-01-10', true, true],
            'before-after' => ['2018-01-11', false, false],
            'before-after-inclusive' => ['2018-01-11', true, false],
            'finish-exact' => ['2018-01-15', false, false],
            'finish-exact-inclusive' => ['2018-01-15', true, false],
        ];
    }

    /**
     * @test
     * @dataProvider provideCanCheckDateBefore
     */
    public function canCheckDateBefore(string $date, bool $inclusive, bool $expected): void
    {
        $range = new DateRange(
            new DateTimeImmutable('2018-01-10'),
            new DateTimeImmutable('2018-01-15')
        );

        self::assertEquals($expected, $range->isDateBefore(new DateTimeImmutable($date), $inclusive));
    }

    /**
     * Data provider.
     *
     * @return mixed[][]
     */
    public function provideCanCheckDateAfter(): array
    {
        return [
            'after' => ['2018-01-20', false, true],
            'after-exact' => ['2018-01-15', false, false],
            'after-exact-inclusive' => ['2018-01-15', true, true],
            'before-after' => ['2018-01-14', false, false],
            'before-after-inclusive' => ['2018-01-14', true, false],
            'start-exact' => ['2018-01-10', false, false],
            'start-exact-inclusive' => ['2018-01-10', true, false],
        ];
    }

    /**
     * @test
     * @dataProvider provideCanCheckDateAfter
     */
    public function canCheckDateAfter(string $date, bool $inclusive, bool $expected): void
    {
        $range = new DateRange(
            new DateTimeImmutable('2018-01-10'),
            new DateTimeImmutable('2018-01-15')
        );

        self::assertEquals($expected, $range->isDateAfter(new DateTimeImmutable($date), $inclusive));
    }

    /**
     * Data provider.
     *
     * @return mixed[][]
     */
    public function provideCanCheckDateWithin(): array
    {
        return [
            'outside-before' => ['2018-01-01', false, false],
            'outside-before-inclusive' => ['2018-01-01', true, false],
            'outside-exact-start' => ['2018-01-10', false, false],
            'outside-exact-start-inclusive' => ['2018-01-10', true, true],

            'inside' => ['2018-01-11', false, true],

            'outside-after' => ['2018-01-20', false, false],
            'outside-after-inclusive' => ['2018-01-20', true, false],
            'outside-exact-finish' => ['2018-01-15', false, false],
            'outside-exact-finish-inclusive' => ['2018-01-15', true, true],
        ];
    }

    /**
     * @test
     * @dataProvider provideCanCheckDateWithin
     */
    public function canCheckDateWithin(string $date, bool $inclusive, bool $expected): void
    {
        $range = new DateRange(
            new DateTimeImmutable('2018-01-10'),
            new DateTimeImmutable('2018-01-15')
        );

        self::assertEquals($expected, $range->isDateWithin(new DateTimeImmutable($date), $inclusive));
    }

    /**
     * Data provider.
     *
     * @return mixed[][]
     */
    public function provideCanCheckDateRangeBeforeInclusive(): array
    {
        return [
            'all' => ['2018-01-01', '2018-01-02', true],
            'finish-exact' => ['2018-01-01', '2018-01-10', true],
            'finish-miss' => ['2018-01-01', '2018-01-11', true],
            'start-exact' => ['2018-01-10', '2018-02-01', true],
            'start-miss' => ['2018-01-11', '2018-02-01', false],
            'all-miss' => ['2018-02-01', '2018-02-01', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideCanCheckDateRangeBeforeInclusive
     */
    public function canCheckDateRangeBeforeInclusive(string $start, string $finish, bool $expected): void
    {
        $range = new DateRange(
            new DateTimeImmutable('2018-01-10'),
            new DateTimeImmutable('2018-01-15')
        );

        $check = new DateRange(
            new DateTimeImmutable($start),
            new DateTimeImmutable($finish)
        );

        self::assertEquals($expected, $range->isRangeBefore($check));
    }

    /**
     * Data provider.
     *
     * @return mixed[][]
     */
    public function provideCanCheckDateRangeBeforeNotInclusive(): array
    {
        return [
            'all' => ['2018-01-01', '2018-01-02', true],
            'finish-exact' => ['2018-01-01', '2018-01-10', true],
            'finish-miss' => ['2018-01-01', '2018-01-11', true],
            'start-exact' => ['2018-01-10', '2018-02-01', false],
            'start-miss' => ['2018-01-11', '2018-02-01', false],
            'all-miss' => ['2018-02-01', '2018-02-01', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideCanCheckDateRangeBeforeNotInclusive
     */
    public function canCheckDateRangeBeforeNotInclusive(string $start, string $finish, bool $expected): void
    {
        $range = new DateRange(
            new DateTimeImmutable('2018-01-10'),
            new DateTimeImmutable('2018-01-15')
        );

        $check = new DateRange(
            new DateTimeImmutable($start),
            new DateTimeImmutable($finish)
        );

        self::assertEquals($expected, $range->isRangeBefore($check, false));
    }

    /**
     * Data provider.
     *
     * @return mixed[][]
     */
    public function provideCanCheckDateRangeAfterInclusive(): array
    {
        return [
            'all' => ['2018-02-01', '2018-02-02', true],
            'finish-exact' => ['2018-01-01', '2018-01-15', true],
            'finish-miss' => ['2018-01-01', '2018-01-14', false],
            'all-miss' => ['2018-01-01', '2018-01-01', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideCanCheckDateRangeAfterInclusive
     */
    public function canCheckDateRangeAfterInclusive(string $start, string $finish, bool $expected): void
    {
        $range = new DateRange(
            new DateTimeImmutable('2018-01-10'),
            new DateTimeImmutable('2018-01-15')
        );

        $check = new DateRange(
            new DateTimeImmutable($start),
            new DateTimeImmutable($finish)
        );

        self::assertEquals($expected, $range->isRangeAfter($check));
    }

    /**
     * Data provider.
     *
     * @return mixed[][]
     */
    public function provideCanCheckDateRangeAfterNotInclusive(): array
    {
        return [
            'all' => ['2018-02-01', '2018-02-02', true],
            'finish-exact' => ['2018-01-01', '2018-01-15', false],
            'finish-miss' => ['2018-01-01', '2018-01-14', false],
            'all-miss' => ['2018-01-01', '2018-01-01', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideCanCheckDateRangeAfterNotInclusive
     */
    public function canCheckDateRangeAfterNotInclusive(string $start, string $finish, bool $expected): void
    {
        $range = new DateRange(
            new DateTimeImmutable('2018-01-10'),
            new DateTimeImmutable('2018-01-15')
        );

        $check = new DateRange(
            new DateTimeImmutable($start),
            new DateTimeImmutable($finish)
        );

        self::assertEquals($expected, $range->isRangeAfter($check, false));
    }

    /**
     * Data provider.
     *
     * @return mixed[][]
     */
    public function provideCanCheckDateRangeWithinInclusive(): array
    {
        return [
            'all-before' => ['2018-01-01', '2018-01-02', false],
            'finishing-on-start' => ['2018-01-01', '2018-01-10', false],
            'finishing-after-start' => ['2018-01-01', '2018-01-11', false],
            'start-on-finishing-after-start' => ['2018-01-10', '2018-01-11', true],
            'start-after-start-finishing-before-finsh' => ['2018-01-11', '2018-01-12', true],
            'start-exact-finishing-exact' => ['2018-01-10', '2018-01-15', true],
            'start-exact-finishing-after' => ['2018-01-10', '2018-01-16', false],
            'start-exact-finish-finishing-after' => ['2018-01-15', '2018-01-16', false],
            'all-after' => ['2018-01-16', '2018-01-17', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideCanCheckDateRangeWithinInclusive
     */
    public function canCheckDateRangeWithinInclusive(string $start, string $finish, bool $expected): void
    {
        $range = new DateRange(
            new DateTimeImmutable('2018-01-10'),
            new DateTimeImmutable('2018-01-15')
        );

        $check = new DateRange(
            new DateTimeImmutable($start),
            new DateTimeImmutable($finish)
        );

        self::assertEquals($expected, $range->isRangeWithin($check, false));
    }

    /**
     * @test
     * @dataProvider provideCanCheckDateRangeWithinInclusive
     */
    public function canCheckDateRangeOutsideInclusive(string $start, string $finish, bool $expected): void
    {
        $range = new DateRange(
            new DateTimeImmutable('2018-01-10'),
            new DateTimeImmutable('2018-01-15')
        );

        $check = new DateRange(
            new DateTimeImmutable($start),
            new DateTimeImmutable($finish)
        );

        self::assertEquals(!$expected, $range->isRangeOutside($check, false));
    }
}
