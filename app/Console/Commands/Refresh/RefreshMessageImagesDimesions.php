<?php

namespace App\Console\Commands\Refresh;

use App\Jobs\MessageImageDimensioning;
use App\Message;
use Illuminate\Console\Command;

class RefreshMessageImagesDimesions extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:message_images_dimesions';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Пересчитаем у изображений отсутствующие размеры';

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
		Message::orderBy('id', 'desc')->chunk(1000, function ($items) {
			foreach ($items as $item) {
				echo('message: ' . $item->id . "\n");

				MessageImageDimensioning::dispatch($item);
			}
		});
	}


}
