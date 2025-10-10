<?php
// app/Helpers/DateHelper.php

namespace App\Helpers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DateHelper
{
    /**
     * Calculate working days between two dates (excluding weekends)
     */
    public static function calculateWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $days = 0;
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            if ($date->isWeekday()) {
                $days++;
            }
        }

        return $days;
    }

    /**
     * Get week start and end dates
     */
    public static function getWeekDates(Carbon $date = null): array
    {
        $date = $date ?? now();

        return [
            'start' => $date->copy()->startOfWeek(),
            'end' => $date->copy()->endOfWeek(),
        ];
    }

    /**
     * Check if date is a bank holiday (UK)
     */
    public static function isBankHoliday(Carbon $date): bool
    {
        // Implement bank holiday logic
        // You could fetch from an API or maintain a config array
        $bankHolidays = config('holidays.uk', []);

        return in_array($date->format('Y-m-d'), $bankHolidays);
    }
}
