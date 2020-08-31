<?php

namespace App\Console\Commands\Old;

use App\User;
use App\UserPhoto;
use Illuminate\Console\Command;
use Storage;

class OldAvatarsToNew extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:avatars {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда перемещает старые аватары в новое место';

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

		$count = User::any()->count();

		$c = (int)ceil($count / $limit);

		for ($i = 0; $i <= $c; $i++) {

			$skip = ($i * $limit);

			$users = User::any()->select('id', 'photo')
				->take($limit)
				->skip($skip)
				->orderBy('id', 'asc')
				->get();

			foreach ($users as $user) {

				$this->user($user);
			}
		}
	}

	function user($user)
	{
		$photoAr = unserialize($user->photo);

		if (isset($photoAr[1]['n'])) {
			$photo_name = $photoAr[1]['n'];

			$dirname = $this->oldStoragePathLocal($user->id);
			$filePath = $this->oldStoragePathLocal($user->id) . '/' . $photo_name;

			if (Storage::disk('old')->exists($filePath)) {

				$avatar = new UserPhoto;
				$avatar->user_id = $user->id;
				$avatar->name = $photo_name;
				$avatar->size = Storage::disk('old')->size($filePath);
				$avatar->created_at = Storage::disk('old')->lastModified($filePath);
				$avatar->updated_at = Storage::disk('old')->lastModified($filePath);
				$avatar->parameters = [
					'w' => $photoAr[1]['w'],
					'h' => $photoAr[1]['h']
				];

				$avatar->dirname = $dirname;
				$avatar->save();

				$user->avatar_id = $avatar->id;
				$user->save();
			}
		}

		/*
				if (isset($photoAr[1]['n'])) {
					$photo_name = $photoAr[1]['n'];
					$photo_path = $this->PathLocal($user->id) . '/' . $photo_name;

					if (file_exists($photo_path)) {
						$avatar = new UserPhoto;
						$avatar->user_id = $user->id;
						$avatar->name = $photo_name;
						$avatar->parameters = [
							'width' => $photoAr[1]['w'],
							'height' => $photoAr[1]['h']
						];
						$avatar->path_to_file = $photo_path;
						$avatar->save();

						//Storage::putFileAs(getPath($user->id), new File($photo_path), $photo_name);
					}
				}
				*/
	}

	function oldStoragePathLocal($Id)
	{
		return 'user_data/' . $this->PathPart($Id);
	}

	function PathPart($Id)
	{
		$Id = intval($Id);

		if (!$Id) return FALSE;

		$f1 = (((ceil($Id / 1000000)) - 1) * 1000000);
		$f2 = (((ceil($Id / 10000)) - 1) * 10000);
		$f3 = (((ceil($Id / 100)) - 1) * 100);

		return $f1 . '/' . $f2 . '/' . $f3 . '/' . $Id;
	}

	function PathLocal($Id)
	{
		return old_data_path() . '/user_data/' . $this->PathPart($Id);
	}
}
