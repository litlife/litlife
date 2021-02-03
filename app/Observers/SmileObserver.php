<?php

namespace App\Observers;

use App\Smile;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Litlife\Url\Url;

class SmileObserver
{
    public function creating(Smile $smile)
    {
        $smile->parameters = [
            'width' => $smile->getRealWidth(),
            'height' => $smile->getRealHeight()
        ];
    }

	public function created(Smile $smile)
	{
        if (empty($smile->dirname))
            $smile->dirname = $smile->getDirname();

        if ($smile->exists())
            throw new \Exception('File ' . $smile->url . ' is storage ' . $smile->storage . ' already exists ');

        if (is_resource($smile->source)) {
            rewind($smile->source);
            Storage::disk($smile->storage)
                ->put($smile->dirname . '/' . $smile->name, $smile->source);
        } elseif (file_exists($smile->source)) {
            Storage::disk($smile->storage)
                ->putFileAs($smile->dirname, new File($smile->source), $smile->name);
        } else {
            throw new \Exception('resource or file not found');
        }

		$this->updateJsonFile();
	}

	public function updateJsonFile()
	{
		Artisan::call('smile:create_json_file');
	}

	public function deleted(Smile $smile)
	{
        if ($smile->isForceDeleting())
            Storage::disk($smile->storage)->delete($smile->dirname . '/' . $smile->name);

		$this->updateJsonFile();
	}

	public function restored(Smile $smile)
	{
		$this->updateJsonFile();
	}
}