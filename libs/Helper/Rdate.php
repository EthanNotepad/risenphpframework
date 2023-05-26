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
}
