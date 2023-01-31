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
}
