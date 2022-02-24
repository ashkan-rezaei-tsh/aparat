<?php
class SiteHelper
{
    public static function toValidMoibileNumber(string $mobile): string
    {
        return '+98' . substr($mobile, -10, 10);
    }
}
