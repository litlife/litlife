<?php

namespace App\Console\Commands\Old;

use App\User;
use App\UserReadStyle;
use Illuminate\Console\Command;

class OldUserReadStyles extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:read_styles {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда переносит стили чтения';

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

			$items = User::select('id', 'read_style')
				->any()
				->take($limit)
				->skip($skip)
				->orderBy('id', 'desc')
				->get();

			foreach ($items as $item) {

				echo($item->id . "\n");

				$this->item($item);
			}
		}
	}

	function item($user)
	{
		if (empty($user->read_style))
			return;

		$ar = unserialize($user->read_style);

		// a:5:{s:11:"font_family";s:5:"Arial";s:10:"text_align";s:7:"justify";s:9:"font_size";s:2:"18";s:16:"background_color";s:7:"#FFFFFF";s:10:"font_color";s:7:"#000000";}

		$style = new UserReadStyle;
		$style->user_id = $user->id;
		$style->font = @$ar['font_family'];
		$style->align = @$ar['text_align'];
		$style->size = @$ar['font_size'];
		$style->background_color = @$ar['background_color'];
		$style->font_color = @$ar['font_color'];
		$style->save();

	}
}
