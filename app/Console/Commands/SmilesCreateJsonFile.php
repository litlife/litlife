<?php

namespace App\Console\Commands;

use App\Enums\VariablesEnum;
use App\Smile;
use App\Variable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SmilesCreateJsonFile extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'smile:create_json_file';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Создает json файл со списком смайликов';

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
		$smiles = $this->getSmiles();

		$json = $smiles->toJson();
		$jsonp = '/**/jsonp(' . $json . ')';

		Storage::delete('smiles/list.json');
		Storage::put('smiles/list.json', $jsonp);

		Variable::updateOrCreate(
			['name' => VariablesEnum::SmilesJsonUrl],
			['value' => Storage::url('smiles/list.json?id=' . md5($jsonp))]
		);
	}

	public function getSmiles()
	{
		$smiles = Smile::considerTime()
			->void()
			->disableModelCaching()
			->get();

		$smiles = $smiles->map(function ($smile) {
			return collect($smile->append('full_url')->toArray())
				->only(['name', 'simple_form', 'full_url'])
				->all();
		});

		return $smiles;
	}
}
