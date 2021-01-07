<?php


namespace App\Traits;


trait DataTrait
{
    public static function formatDate($date)
    {
        return date('Y-m-d', strtotime(str_replace('/', '-', $date)));
    }

    public static function formatMoney($money)
    {
        return number_format((preg_replace("/[^0-9]/", "", $money)), 2, '.', '');
    }

    public static function format_number($number)
    {
        return preg_replace("/[^0-9]/", ".", preg_replace("/[^0-9,]/", "", $number));
    }

    public static function replaceSpacesByPercentage($str)
    {
        return str_replace(" ","%",$str);
    }
}
