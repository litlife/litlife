<?php

namespace App\Console\Commands\Refresh;

use App\Events\BookFilesCountChanged;
use App\Image;
use Exception;
use Illuminate\Console\Command;
use Imagick;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;

class RefreshImagesHash extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:images_hash {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет хеши у изображений';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$limit = $this->argument('limit');

		Image::any()->orderBy('id', 'desc')->chunk($limit, function ($items) {
			foreach ($items as $item) {
				$this->item($item);
			}
		});
	}

	function item($item)
	{
		if ($item->exists()) {
			try {

				$hasher = new ImageHash(new DifferenceHash());
				$item->phash = $hasher->hash($item->getStream());

				$img = new Imagick();
				$img->readImageFile($item->getStream());
				$item->sha256_hash = $img->getImageSignature();

				$item->save();

				echo($item->url . ' sha256:' . $item->sha256_hash . ' phash:' . $item->phash . "\n");
			} catch (Exception $exception) {

			}


		}
	}
}
