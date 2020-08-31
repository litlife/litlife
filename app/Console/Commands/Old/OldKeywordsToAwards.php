<?php

namespace App\Console\Commands\Old;

use App\Award;
use App\Book;
use App\BookAward;
use App\Keyword;
use Illuminate\Console\Command;

class OldKeywordsToAwards extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:keywords_to_awards';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда переносит премии из ключевых слов в специальные поля';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$awards_titles = [
			'АБС-премия',
			'Августовская премия',
			'Большая премия Жана Жионо',
			'Букеровская премия',
			'Всемирная премия фэнтези',
			'Гонкуровская премия',
			'Дублинская литературная премия',
			'Европейская книжная премия',
			'Золотой дубль',
			'Лауреаты Нобелевской премии',
			'Лауреаты премии Эдгара По',
			'Премия Livre Inter',
			'Премия Антибукер',
			'Премия Аполло',
			'Премия Астрея',
			'Премия Аэлита',
			'Премия Балрог',
			'Премия Басткон',
			'Премия Библиотеки Бреве',
			'Премия Большая книга',
			'Премия Бронзовая Улитка',
			'Премия Брэма Стокера',
			'Премия Великое Кольцо',
			'Премия Гёте города Франкфурт',
			'Премия Дебют',
			'Премия Декабрь',
			'Премия Заветная мечта',
			'Премия Звездный мост',
			'Премия имени И. А. Ефремова',
			'Премия имени Н. В. Гоголя',
			'Премия Интерпресскон',
			'Премия И. П. Белкина',
			'Премия Кампьелло',
			'Премия Кира Булычева',
			'Премия Локус',
			'Премия Мечи',
			'Премия Мраморный фавн',
			'Премия Надаля',
			'Премия Национальный бестселлер',
			'Премия Небьюла',
			'Премия Оранж',
			'Премия Планета',
			'Премия Ренодо',
			'Премия Ромуло Гальегоса',
			'Премия РосКон',
			'Премия Северная Пальмира',
			'Премия Северного Совета',
			'Премия Серебряная стрела',
			'Премия Сигма-Ф',
			'Премия Сомерсета Моэма',
			'Премия Стеклянный ключ',
			'Премия Странник',
			'Премия Стрега',
			'Премия Теодора Старджона',
			'Премия Французской академии',
			'Премия Хьюго',
			'Премия Ясная Поляна',
			'Пулитцеровская премия',
			'Русская премия',
			'Русский Букер'
		];

		foreach ($awards_titles as $awards_title) {
			if (!Award::where('title', $awards_title)->count()) {
				$award = new Award();
				$award->fill(['title' => $awards_title]);
				$award->create_user_id = 1;
				$award->save();
			}
		}

		Award::orderBy('id')->chunk(100, function ($awards) {
			foreach ($awards as $award) {
				$this->award($award);
			}
		});
	}

	public function award($award)
	{
		$keywords = Keyword::where('text', $award->title)->get();

		$keywords->each(function ($keyword) use ($award) {

			$books = Book::select('books.*')
				->join('book_keywords', function ($join) {
					$join->on("book_keywords.book_id", '=', 'books.id');
				})
				->whereIn("book_keywords.keyword_id", [$keyword->id])
				->get();

			$books->each(function ($book) use ($award) {

				$book_award = BookAward::where('book_id', $book->id)->where('award_id', $award->id)->count();

				if (empty($book_award)) {
					$book_award = new BookAward;
					$book_award->award_id = $award->id;
					$book_award->book_id = $book->id;
					$book_award->create_user_id = 1;
					$book_award->save();
				}
			});
		});
	}
}
