<?php

namespace App\Helpers;

use \Hashids\Hashids;

class SiteHelper
{
    public static function toValidMoibileNumber(string $mobile): string
    {
        return '+98' . substr($mobile, -10, 10);
    }

    public static function generateVerificationCode()
    {
        return random_int(10000, 99999);
    }

    public static function uniqueId(int $value)
    {
        $hashids = new Hashids('', 10);
        return $hashids->encode($value);
    }
}
