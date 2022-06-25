<?php

namespace App\Helpers;

use \Hashids\Hashids;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SiteHelper
{
	public static function toValidMobileNumber(string $mobile): string
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
	
	public static function clearDisk(string $storageName)
	{
		try{
			Storage::disk($storageName)->delete(Storage::disk($storageName)->allFiles());
			foreach(Storage::disk($storageName)->allDirectories() as $dir){
				Storage::disk($storageName)->deleteDirectory($dir);
			}
			return true;
		}catch(\Exception $exception){
			Log::error($exception);
			return false;
		}
	}
}
