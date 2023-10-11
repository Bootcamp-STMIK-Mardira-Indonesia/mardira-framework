<?php

namespace App\Helpers;

class TimeHelper
{
    /**
     * Set minutes
     *
     * @param  mixed $minutes
     * @return int
     */
    public static function setMinutes(int $minutes): int
    {
        return time() + ($minutes * 60);
    }

    /**
     * Set hours
     *
     * @param  mixed $hours
     * @return int
     */
    public static function setHours(int $hours): int
    {
        return time() + ($hours * 60 * 60);
    }


    public static function setDays(int $days): int
    {
        return time() + ($days * 60 * 60 * 24);
    }

    /**
     * Set weeks
     *
     * @param  mixed $weeks
     * @return int
     */
    public static function setWeeks(int $weeks): int
    {
        return time() + ($weeks * 60 * 60 * 24 * 7);
    }
}
