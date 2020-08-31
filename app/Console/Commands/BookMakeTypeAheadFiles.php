<?php

namespace App\Console\Commands;

use App\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BookMakeTypeAheadFiles extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'book:make_typeahead_files';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Функция создает файлы для быстрых предложений';

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
		$this->bookPublishers();
		$this->bookCities();
		$this->bookRightholders();
	}

	public function bookPublishers()
	{
		$collection = Book::accepted()
			->selectRaw('"pi_pub" as value, count("pi_pub") as count')
			->where("pi_pub", '!=', '')
			->whereNotNull("pi_pub")
			->groupBy("pi_pub")
			->havingRaw('count("pi_pub") > 5')
			->orderBy('count', 'desc')
			->limit(1000)
			->get();

		$array = [];

		foreach ($collection as $key => $item) {
			$array[$key] = [
				'value' => $item->value
			];
		}

		Storage::delete('typeahead/book/publishers.json');
		Storage::put('typeahead/book/publishers.json', json_encode($array));
	}

	public function bookCities()
	{
		$collection = Book::accepted()
			->selectRaw('"pi_city" as value, count("pi_city") as count')
			->where("pi_city", '!=', '')
			->whereNotNull("pi_city")
			->groupBy("pi_city")
			->havingRaw('count("pi_city") > 5')
			->orderBy('count', 'desc')
			->limit(1000)
			->get();

		$array = [];

		foreach ($collection as $key => $item) {
			$array[$key] = [
				'value' => $item->value
			];
		}

		Storage::delete('typeahead/book/cities.json');
		Storage::put('typeahead/book/cities.json', json_encode($array));
	}

	public function bookRightholders()
	{
		$collection = Book::accepted()
			->selectRaw('"rightholder" as value, count("rightholder") as count')
			->where("rightholder", '!=', '')
			->whereNotNull("rightholder")
			->groupBy("rightholder")
			->havingRaw('count("rightholder") > 5')
			->orderBy('count', 'desc')
			->limit(1000)
			->get();

		$array = [];

		foreach ($collection as $key => $item) {
			$array[$key] = [
				'value' => $item->value
			];
		}

		Storage::delete('typeahead/book/rightholders.json');
		Storage::put('typeahead/book/rightholders.json', json_encode($array));
	}
}
