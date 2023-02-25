<?php

namespace App\lib;

class TimeLib
{
    public static function time_to_date($s,$format="Y-m-d H:i:s")
    {
        return $s?date($format,$s):"";
    }
}
