<?php

namespace App\Helpers;

class Helper {

    static function stringArrayConvertToIntArray($array)
    {
        if (isset($array) && $array) {
            if (is_array($array)) {
                foreach ($array as $key => $value) {
                    $array[$key] = intval($value);
                }
            } else {
                $array = [intval($array)];
            }
            return $array;
        }
    }

    static function pathFile($type)
    {
        return "pictures/{$type}";
    }

    static function quickRandomNumber($length = 16)
    {
        $pool = '0123456789';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
}