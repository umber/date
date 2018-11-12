<?php

declare(strict_types=1);

namespace Umber\Date\Range;

use Umber\Date\Exception\Range\DateRangeCannotCreatePeriodException;
use Umber\Date\Exception\Range\DateRangeInvalidException;

use DateInterval;
use DatePeriod;
use DateTimeInterface;
use IteratorAggregate;
use Traversable;

final class DateRange implements DateRangeInterface, IteratorAggregate
{
    private $start;
    private $finish;
    private $interval;

    public function __construct(?DateTimeInterface $start, ?DateTimeInterface $finish, ?DateInterval $interval = null)
    {
        if ($start === null && $finish === null) {
            throw DateRangeInvalidException::create();
        }

        $this->start = $start;
        $this->finish = $finish;
        $this->interval = $interval;
    }

    /**
     * {@inheritdoc}
     */
    public function start(): ?DateTimeInterface
    {
        return $this->start;
    }

    /**
     * {@inheritdoc}
     */
    public function finish(): ?DateTimeInterface
    {
        return $this->finish;
    }

    /**
     * {@inheritdoc}
     */
    public function interval(): ?DateInterval
    {
        return $this->interval;
    }

    /**
     * {@inheritdoc}
     */
    public function period(bool $inclusive = true): DatePeriod
    {
        if ($this->start === null || $this->finish === null) {
            throw DateRangeCannotCreatePeriodException::create();
        }

        [$start, $finish] = [$this->start, $this->finish];

        if ($inclusive === false) {
            $start = $this->start->add($this->interval);
            $finish = $this->finish->sub($this->interval);
        }

        // Experimentation suggests the period will not include the finish date.
        // Therefore lets move the finish date by a second.
        $finish = $finish->modify('+1 second');

        return new DatePeriod($start, $this->interval, $finish);
    }

    /**
     * {@inheritdoc}
     */
    public function isOpenEnded(): bool
    {
        return $this->start === null
            || $this->finish === null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOpenStart(): bool
    {
        return $this->start === null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOpenFinish(): bool
    {
        return $this->finish === null;
    }

    /**
     * {@inheritdoc}
     */
    public function isDateWithin(DateTimeInterface $date, bool $inclusive = true): bool
    {
        if ($this->isDateBefore($date, !$inclusive)) {
            return false;
        }

        if ($this->isDateAfter($date, !$inclusive)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isDateBefore(DateTimeInterface $date, bool $inclusive = true): bool
    {
        if ($inclusive === false) {
            return $date < $this->start;
        }

        return $date <= $this->start;
    }

    /**
     * {@inheritdoc}
     */
    public function isDateAfter(DateTimeInterface $date, bool $inclusive = true): bool
    {
        if ($inclusive === false) {
            return $date > $this->finish;
        }

        return $date >= $this->finish;
    }

    /**
     * {@inheritdoc}
     */
    public function isRangeWithin(DateRangeInterface $range, bool $inclusive = true): bool
    {
        if ($this->isRangeBefore($range, $inclusive)) {
            return false;
        }

        if ($this->isRangeAfter($range, $inclusive)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isRangeOutside(DateRangeInterface $range, bool $inclusive = true): bool
    {
        return !$this->isRangeWithin($range, $inclusive);
    }

    /**
     * {@inheritdoc}
     */
    public function isRangeColliding(DateRangeInterface $range, bool $inclusive = true): bool
    {
        // TODO: Implement isRangeColliding() method.
    }

    /**
     * {@inheritdoc}
     */
    public function isRangeBefore(DateRangeInterface $range, bool $inclusive = true): bool
    {
        return $this->isDateBefore($range->start(), $inclusive);
    }

    /**
     * {@inheritdoc}
     */
    public function isRangeAfter(DateRangeInterface $range, bool $inclusive = true): bool
    {
        return $this->isDateAfter($range->finish(), $inclusive);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Traversable
    {
        return $this->period();
    }
}
