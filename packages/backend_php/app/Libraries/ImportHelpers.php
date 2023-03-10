<?php

namespace App\Libraries;

class ImportHelpers
{
    const MSG_EMPTY = "{0} is required";
    const MSG_VALID = "{0} is invalid";
    const MSG_MAX_LENGTH = "{0} has max length of {1}";
    const MSG_DUPLICATE = "The {0} is existed!";
    const MSG_NOT_EXIST = "{0} does not exist";
    const MSG_NOT_NUMBER = "The {0} is must be a number";
    const MSG_MIN_NUMBER = "The {0} is must be greater than 0";
    const MSG_DATE_FORMAT = "The {0} is invalid format {1} or {2}";
    const ERROR_COLUMN = "Error";

    public static function checkRowEmpty($data)
    {
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                return false;
            }
        }

        return true;
    }

    public static function isDate($date)
    {
        if (!strtotime($date)) {
            return false;
        }

        $isValidDateData = $isValidDateTool = false;

        if (count(explode("-", $date)) == 3) {
            $date = date('Y-m-d', strtotime($date));
            list($year, $month, $day) = explode("-", $date);
            $isValidDateData = checkdate($month, $day, $year);
        }

        if (count(explode("/", $date)) == 3) {
            $date = date('m/d/Y', strtotime($date));
            list($month, $day, $year) = explode("/", $date);
            $isValidDateTool = checkdate($month, $day, $year);
        }

        if ($isValidDateData || $isValidDateTool) {
            return true;
        }
        return false;
    }
}