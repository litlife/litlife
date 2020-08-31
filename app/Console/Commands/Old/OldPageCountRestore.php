<?php

namespace App\Console\Commands\Old;

use App\Book;
use App\Library\Old\xsBookPath;
use Exception;
use Illuminate\Console\Command;
use SQLite3;
use const SQLITE3_ASSOC;

class OldPageCountRestore extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:old_pages_count_restore {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда восстанавливает из sqlite базы данных количество страниц';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Book::any()
			->where('page_count', '<', 1)
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					echo($item->id . "\n");
					$this->book($item);
				}
			});
	}

	public function book($book)
	{
		if (!$book->isPagesNewFormat()) {
			$bd_path = xsBookPath::GetPathToSqliteDB($book->id);

			if (empty($bd_path))
				return false;

			try {
				$db = new SQLite3($bd_path);

				$pages_count = (function () use ($db) {
					$statement = $db->prepare('SELECT count(*) FROM pages LIMIT 1');
					return pos($statement->execute()->fetchArray(SQLITE3_ASSOC));
				})();

				$book->page_count = $pages_count;

			} catch (Exception $exception) {
				return false;
			}
		}

		$book->save();
	}
}
