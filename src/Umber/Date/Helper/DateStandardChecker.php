<?php

declare(strict_types=1);

namespace Umber\Date\Helper;

use RuntimeException;

final class DateStandardChecker
{
    public const STANDARD_ISO_8601_SIMPLISTIC = 'iso-8601-simplistic';
    public const STANDARD_ISO_8601_SIMPLISTIC_TIMEZONE = 'iso-8601-simplistic-timezone';

    private static $rules = [
        self::STANDARD_ISO_8601_SIMPLISTIC => '/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$/',
        self::STANDARD_ISO_8601_SIMPLISTIC_TIMEZONE => '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(Z|(\-|\+)\d{2}:\d{2})$/',
    ];

    /**
     * Check is valid against the standard provided.
     */
    public static function check(string $standard, string $date): bool
    {
        if (!isset(self::$rules[$standard])) {
            throw new RuntimeException('no standard');
        }

        return preg_match(self::$rules[$standard], $date) === 1;
    }
}
