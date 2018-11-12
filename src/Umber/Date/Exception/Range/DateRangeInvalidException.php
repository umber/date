<?php

declare(strict_types=1);

namespace Umber\Date\Exception\Range;

use RuntimeException;

final class DateRangeInvalidException extends RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public static function create(): self
    {
        return new self(implode(' ', [
            'A date range cannot be constructed with null start and finish dates.',
            'Please provide at least one side of the date range.',
        ]));
    }
}
