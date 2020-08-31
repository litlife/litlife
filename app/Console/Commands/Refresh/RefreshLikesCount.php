<?php

namespace App\Console\Commands\Refresh;

use Illuminate\Console\Command;

class RefreshLikesCount extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:likes_count';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Пересчитаем количество лайков  ';

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
		$models = [
			'App\Blog',
			'App\Post',
			'App\Author',
			'App\Book',
			'App\Sequence'
		];

		foreach ($models as $model) {
			$this->model($model);
		}
	}

	function model($model)
	{
		$model::orderBy('id')->chunk(1000, function ($items) use ($model) {
			foreach ($items as $item) {
				$this->item($item, $model);
			}
		});
	}

	function item($item, $model)
	{
		echo($model . ' ' . $item->id . "\n");
		$item->like_count = $item->likes()->count();
		$item->save();
	}
}
