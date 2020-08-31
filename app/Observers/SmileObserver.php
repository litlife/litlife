<?php

namespace App\Observers;

use App\Smile;
use Illuminate\Support\Facades\Artisan;

class SmileObserver
{
	public function created(Smile $smile)
	{
		$this->updateJsonFile();
	}

	public function updateJsonFile()
	{
		Artisan::call('smile:create_json_file');
	}

	public function deleted(Smile $smile)
	{
		$this->updateJsonFile();
	}

	public function restored(Smile $smile)
	{
		$this->updateJsonFile();
	}
}