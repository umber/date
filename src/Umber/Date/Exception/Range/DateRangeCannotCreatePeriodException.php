<?php

declare(strict_types=1);

namespace Umber\Date\Exception\Range;

use RuntimeException;

final class DateRangeCannotCreatePeriodException extends RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public static function create(): self
    {
        return new self(implode(' ', [
            'A date period cannot be constructed when one of the start of finish dates are null.',
            'Please provide both start and finish dates.',
        ]));
    }
}
