<?php

namespace App\Console\Commands\Old;

use App\Author;
use Illuminate\Console\Command;

class OldAuthorPhotosToNew extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:author_photos {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда перемещает старые фото автора в новое местоположение';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$limit = $this->argument('limit');

		$count = Author::any()->count();

		$c = (int)ceil($count / $limit);

		for ($i = 0; $i <= $c; $i++) {

			$skip = ($i * $limit);

			$authors = Author::any()->take($limit)
				->skip($skip)
				->orderBy('id', 'asc')
				->get();

			foreach ($authors as $author) {

				$this->author($author);
			}
		}
	}

	function author($author)
	{
		foreach ($author->photos as $photo) {
			$photo->dirname = $this->oldStoragePath($author->id);
			$photo->save();

			if ($photo->type == '2') {
				$author->photo_id = $photo->id;
				$author->save();
			}
		}


		/*
		 * до этого я решил полностью перенести файлы, а сейчас я думаю просто записать ссылки в базу данных
		 *
		 *
		foreach ($author->photos as $author_photo) {
			$old_author_path = $this->PathLocal($author->id) . '/' . $author_photo->name;

			if (file_exists($old_author_path)) {
				Storage::putFileAs(
					getPath($author->id) . '/' . $author->photo->folder,
					new File($old_author_path),
					$author_photo->name);

				echo('Перенесена из ' . $old_author_path . ' в ' . getPath($author->id) . '/' . $author->photo->folder . '/' . $author_photo->name . " \n");
			}
		}
		*/
	}

	function oldStoragePath($Id)
	{
		return '/Author/' . $this->PathPart($Id);
	}

	static function PathPart($Id)
	{
		$f1 = (((ceil($Id / 1000)) - 1) * 1000);
		//$f2 = (((ceil($Id/100))-1)*100);

		return $f1 . '/' . $Id;
	}

	function PathLocal($Id)
	{
		return old_data_path() . '/Author/' . $this->PathPart($Id);
	}
}
