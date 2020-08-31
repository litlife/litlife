<?php

namespace App\Console\Commands;

use App\UserPhoto;
use Illuminate\Console\Command;

class TrashClear extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'trash:clear';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Окончательно удаляет "мягко" удаленные файлы';

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
		UserPhoto::onlyTrashed()->chunkById(100, function ($items) {
			$items->each(function ($photo) {
				$photo->forceDelete();
			});
		});
	}
}
