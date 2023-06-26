<?php

namespace libs\Helper;

class Rdate
{
    function calculateTimeSum($hours, $minutes, $seconds)
    {
        $totalSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;

        if ($totalSeconds > 838 * 3600) {
            $totalSeconds = 838 * 3600; // Set the maximum value to 838 hours
        }

        $resultHours = floor($totalSeconds / 3600);
        $resultMinutes = floor(($totalSeconds % 3600) / 60);
        $resultSeconds = $totalSeconds % 60;

        return [
            'hours' => $resultHours,
            'minutes' => $resultMinutes,
            'seconds' => $resultSeconds
        ];
    }

    /**
     * @Description get current week start date and end date
     * @DateTime 2023-06-06
     * @return array
     */
    function currentWeek($week = '', $year = '')
    {
        if (empty($week)) {
            $week = date('W');
        }
        if (empty($year)) {
            $year = date('Y');
        }
        $date = new \DateTime();
        $date->setISODate($year, $week);
        $start = $date->format('Y-m-d');
        $date->modify('+6 days');
        $end = $date->format('Y-m-d');
        return [
            'start' => $start,
            'end' => $end
        ];
    }

    /**
     * @Description get current week number
     * @DateTime 2023-06-26
     * @return int
     */
    function numberOfCurrentWeek($date = '')
    {
        if (empty($date)) {
            $date = date('Y-m-d');
        }
        $firstDayOfYear = date('Y-01-01', strtotime($date));
        $firstDayOfYearWeek = date('W', strtotime($firstDayOfYear)); // orginal week number of first day of year

        $currentWeek = date('W', strtotime($date));

        if ($currentWeek < $firstDayOfYearWeek) {
            $currentWeek += 52;
        }

        $weekNumber = $currentWeek - $firstDayOfYearWeek + 1;

        return $weekNumber;
    }
}
