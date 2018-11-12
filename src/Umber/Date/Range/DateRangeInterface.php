<?php

declare(strict_types=1);

namespace Umber\Date\Range;

use Umber\Date\Exception\Range\DateRangeCannotCreatePeriodException;

use DateInterval;
use DatePeriod;
use DateTimeInterface;

interface DateRangeInterface
{
    /**
     * Return the start date.
     */
    public function start(): ?DateTimeInterface;

    /**
     * Return the finish date.
     */
    public function finish(): ?DateTimeInterface;

    /**
     * Return the date interval.
     */
    public function interval(): ?DateInterval;

    /**
     * Return the date range as an internal date period.
     *
     * @param bool $inclusive If the start and end date should be included.
     *
     * @throws DateRangeCannotCreatePeriodException
     */
    public function period(bool $inclusive): DatePeriod;

    /**
     * Does the date range have an open start or finish date.
     */
    public function isOpenEnded(): bool;

    /**
     * Does the date range have an open start date.
     */
    public function hasOpenStart(): bool;

    /**
     * Does the date range have an open finish date.
     */
    public function hasOpenFinish(): bool;

    /**
     * Check if the given date exists within the date range.
     */
    public function isDateWithin(DateTimeInterface $date, bool $inclusive = true): bool;

    /**
     * Check if the given date is before the date range.
     */
    public function isDateBefore(DateTimeInterface $date, bool $inclusive = true): bool;

    /**
     * Check if the given date is after the date range.
     */
    public function isDateAfter(DateTimeInterface $date, bool $inclusive = true): bool;

    /**
     * Check if the given date range is enclosed by this date range.
     */
    public function isRangeWithin(DateRangeInterface $range, bool $inclusive = true): bool;

    /**
     * Check if the given date range encloses this date range.
     */
    public function isRangeOutside(DateRangeInterface $range, bool $inclusive = true): bool;

    /**
     * Check if the given date range collides with the date range.
     */
    public function isRangeColliding(DateRangeInterface $range, bool $inclusive = true): bool;

    /**
     * Check if the given date range starts before this date range.
     */
    public function isRangeBefore(DateRangeInterface $range, bool $inclusive = true): bool;

    /**
     * Check if the given date range ends after this date range.
     */
    public function isRangeAfter(DateRangeInterface $range, bool $inclusive = true): bool;
}
