<?php

declare(strict_types=1);

namespace Umber\Date\Tests\Unit\Helper;

use Umber\Date\Helper\DateStandardChecker;

use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * @covers \Umber\Date\Helper\DateStandardChecker
 */
final class DateStandardCheckerTest extends TestCase
{
    /**
     * Data provider.
     *
     * @return mixed[][]
     */
    public function provideCanCheckValidDateString(): array
    {
        $tests = [];

        // ISO 8601 Simplistic
        $standard = DateStandardChecker::STANDARD_ISO_8601_SIMPLISTIC;
        $tests = array_merge($tests, [
            'iso8601-s-empty' => [$standard, '', false],
            'iso8601-s-invalid-date-01' => [$standard, '2', false],
            'iso8601-s-invalid-date-02' => [$standard, '20', false],
            'iso8601-s-invalid-date-03' => [$standard, '201', false],
            'iso8601-s-invalid-date-04' => [$standard, '2018', false],
            'iso8601-s-invalid-date-05' => [$standard, '2018 01', false],
            'iso8601-s-invalid-date-06' => [$standard, '2018-01', false],
            'iso8601-s-invalid-date-07' => [$standard, '2018-01-1', false],
            'iso8601-s-invalid-date-08' => [$standard, '2018-01 01', false],
            'iso8601-s-valid-date' => [$standard, '2018-01-01', true],
            'iso8601-s-invalid-date-time-01' => [$standard, '2018-01-01 1', false],
            'iso8601-s-invalid-date-time-02' => [$standard, '2018-01-01 01', false],
            'iso8601-s-invalid-date-time-03' => [$standard, '2018-01-01 01:01', false],
            'iso8601-s-invalid-date-time-04' => [$standard, '2018-01-01 01-01-01', false],
            'iso8601-s-valid-date-time' => [$standard, '2018-01-01 11:11:11', true],
        ]);

        // ISO 8601 Simplistic Timezone
        $standard = DateStandardChecker::STANDARD_ISO_8601_SIMPLISTIC_TIMEZONE;
        $tests = array_merge($tests, [
            'iso8601-st-invalid-timezone-01' => [$standard, '2018-01-01T11:22:33', false],
            'iso8601-st-invalid-timezone-02' => [$standard, '2018-01-01P11:22:33', false],
            'iso8601-st-invalid-timezone-03' => [$standard, '2018-01-01T11:22:33X', false],
            'iso8601-st-valid-timezone-zulu' => [$standard, '2018-01-01T11:22:33Z', true],
            'iso8601-st-invalid-timezone-offset-01' => [$standard, '2018-01-01T11:22:33 10:00', false],
            'iso8601-st-invalid-timezone-offset-02' => [$standard, '2018-01-01T11:22:33.10:00', false],
            'iso8601-st-invalid-timezone-offset-03' => [$standard, '2018-01-01T11:22:33/10:00', false],
            'iso8601-st-invalid-timezone-offset-04' => [$standard, '2018-01-01T11:22:33:10:00', false],
            'iso8601-st-invalid-timezone-offset-05' => [$standard, '2018-01-01T11:22:33+1000', false],
            'iso8601-st-invalid-timezone-offset-06' => [$standard, '2018-01-01T11:22:33-1000', false],
            'iso8601-st-valid-timezone-offset-positive' => [$standard, '2018-01-01T11:22:33+10:00', true],
            'iso8601-st-valid-timezone-offset-negative' => [$standard, '2018-01-01T11:22:33-10:00', true],
        ]);

        return $tests;
    }

    /**
     * @test
     * @dataProvider provideCanCheckValidDateString
     */
    public function canCheckValidDateString(string $standard, string $date, bool $expected): void
    {
        $response = DateStandardChecker::check($standard, $date);
        self::assertEquals($expected, $response);
    }
}
