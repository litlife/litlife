<?php

namespace App\Console\Commands\Book;

use App\Page;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BookDeleteAllPagesWhereSectionWasNotFouncCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'book:delete_all_pages_where_section_was_not_found {latest_page_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда удаляет все страницы онлайн чтения у которых не были найдены главы';

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
		Page::where('id', '>=', $this->argument('latest_page_id'))
			->with(['section' => function ($query) {
				$query->any();
			}])
			->whereDoesntHave('section', function (Builder $query) {
				$query->any();
			})
			->chunkById(100, function ($items) {
				foreach ($items as $item) {
					DB::transaction(function () use ($item) {
						$this->item($item);
					});
				}
			});
	}

	public function item(Page $page)
	{
		$section = $page->section;

		if (!empty($section))
			return false;

		$page->delete();

		$this->info('Удалена страница ID: ' . $page->id . ' Глава ID: ' . $page->section_id . ' Книга ID: ' . $page->book_id);
	}
}
