<?php

namespace App\Console\Commands\Refresh;

use App\Smile;
use Illuminate\Console\Command;

class RefreshSmilesWidthHeight extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:smiles_width_height';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Обновляет размеры смайлов';

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
		$smiles = Smile::all();

		foreach ($smiles as $smile) {
			$smile->freshImageSize();
		}
	}
}
