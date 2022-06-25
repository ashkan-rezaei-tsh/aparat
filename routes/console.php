<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function(){
	$this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('aparat:clear', function(){
	SiteHelper::clearDisk('videos');
	$this->info('Clear uploaded video files');
	
	SiteHelper::clearDisk('categories');
	$this->info('Clear uploaded category files');
	
	SiteHelper::clearDisk('channel');
	$this->info('Clear uploaded channel files');
})->purpose('Clear all temporary files');
