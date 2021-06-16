<?php

namespace App\Library;

use App\Award;
use App\Enums\ReadStatus;
use App\Genre;
use App\Keyword;
use Illuminate\Http\Request;

class BookSearchResource extends SearchResource
{
	public $genres;
	public $andGenres;
	public $exclude_genres;
	public $defaultSorting = 'rating_avg_down';
	public $simple_paginate = true;
	public $view_name = 'book.list.default';
	public $keywords;
	protected $book_genres_joined = false;
	private $saveSettings = false;

	public function __construct(Request $request, $query)
	{
		parent::__construct($request, $query);

		$this->setDefaultInputValue('read_access', 'any');
		$this->setDefaultInputValue('download_access', 'any');
		$this->setDefaultInputValue('hide_grouped', 0);
		$this->setDefaultInputValue('view', 'gallery');
	}

	public function renderAjax($vars)
	{
		return view($this->view_name, $vars);
	}

	public function view()
	{
		$vars = $this->getVars();

		if ($this->simple_paginate)
			$vars['books'] = $this->query->simplePaginate();
		else
			$vars['books'] = $this->query->paginate();

		if ($this->request->ajax())
			return view($this->view_name, $vars);

		return view('book.search', $vars);
	}

	public function getVars()
	{
		$this->searchParameters();

		$this->selectDefaultOrder();

		$this->vars = array_merge($this->vars, [
			'input' => $this->input,
			'resource' => $this,
			'genres' => $this->genres,
			'and_genres' => $this->andGenres ?? null,
			'keywords' => $this->keywords ?? null,
			'exclude_genres' => $this->exclude_genres,
			'view' => $this->view,
			'order_array' => $this->order_array,
			'disabled_filters' => $this->disabled_filters,
			'award' => $award ?? null,
			'view_name' => $this->view_name
		]);

		return $this->vars;
	}

	public function searchParameters()
	{
		$this->input = $this->request->all(['search', 'genre', 'and_genres', 'kw', 'language', 'originalLang', 'write_year_after',
			'write_year_before', 'publish_year_after', 'publish_year_before', 'rs', 'order', 'Formats', 'exclude_genres', 'SI_disable', 'hc', 'CoverExists',
			'AnnotationExists', 'read_access', 'download_access', 'paid_access', 'si', 'lp', 'pages_count_min', 'pages_count_max',
			'status', 'author_gender',
			'publish_city', 'comments_exists', 'hide_grouped', 'view', 'read_status', 'award', 'status_of_publication', 'per_page']);

		if (!in_array($this->getInputValue('view'), ['table', 'gallery']))
			$this->setInputValue('view', 'gallery');

		if (is_array($this->input['order']))
			$this->input['order'] = pos($this->input['order']);

		$this->query->select('books.*');

		$this->query->with(["authors", "genres", "sequences", "language",
			"originalLang", "short_annotation", 'group', 'cover', 'authors.managers', 'parse']);

		$this->query->with(['remembered_pages' => function ($query) {
			$query->where('user_id', auth()->id());
		}]);

		$this->query->with(['purchases' => function ($query) {
			$query->where('buyer_user_id', auth()->id());
		}]);

		if ($this->input['search']) {
			if (mb_strlen($this->input['search']) <= 2)
				$this->query->where('title', 'ILIKE', $this->input['search'] . '%');
			else
				$this->query->titleFulltextSearch($this->input['search']);
		}

		$this->genres();
		$this->andGenres();
		$this->exclude_genres();

		// жанры из черного списка
		if (auth()->check()) {
			$genres_blacklist = auth()->user()->genres_blacklist->pluck('genre_id')->toArray();

			if ($genres_blacklist) {
				$this->query->withoutGenre($genres_blacklist);
			}
		}

		if (!empty($this->input['author_gender'])) {

			if (in_array($this->input['author_gender'], ['male', 'female'])) {
				$this->query->whereHas('writers', function ($query) {
					$query->where('gender', $this->input['author_gender']);
				});
			}
		}

		if (!empty($this->input['award']) and !empty($award = Award::where('title', $this->input['award'])->first())) {

			$this->query->join('book_awards', function ($join) use ($award) {
				$join->on('books.id', '=', 'book_awards.book_id')
					->where('book_awards.award_id', $award->id);
			});
		}

		if ($this->input['language'])
			$this->query->where('ti_lb', $this->input['language']);

		if ($this->input['originalLang'])
			$this->query->where('ti_olb', $this->input['originalLang']);

		if ($this->input['originalLang'])
			$this->query->where('ti_olb', $this->input['originalLang']);

		if (!empty($this->input['publish_year_after']) or !empty($this->input['publish_year_before']))
			$this->query->wherePublishYearRange($this->input['publish_year_after'], $this->input['publish_year_before']);

		if (!empty($this->input['write_year_after']) or !empty($this->input['write_year_before']))
			$this->query->whereWriteYearRange($this->input['write_year_after'], $this->input['write_year_before']);

		if ($this->input['publish_city'])
			$this->query->publishCityILike($this->input['publish_city']);

		if (!empty($this->input['pages_count_min']) or !empty($this->input['pages_count_max'])) {
			$this->query->wherePagesCountRange($this->input['pages_count_min'], $this->input['pages_count_max']);
		}

		if ($this->ifFilterEnabled('read_status')) {

			if (auth()->check() and $this->input['read_status'] and (ReadStatus::hasValue($this->input['read_status']) or $this->input['read_status'] == 'no_status')) {

				$this->query->leftJoin('book_statuses', function ($join) {
					$join->on('books.id', 'book_statuses.book_id')
						->where("book_statuses.user_id", auth()->id());
				});

				if ($this->input['read_status'] == 'no_status') {
					$this->query->where(function ($query) {
						$query->where('book_statuses.status', 'null')
							->orWhereNull('book_statuses.status');
					});
				} else {
					$this->query->where('book_statuses.status', $this->input['read_status']);
				}
			}
		}

		switch (mb_strtolower($this->input['CoverExists'])) {
			case 'yes':
				$this->query->whereNotNull('cover_id');
				break;
			case 'no':
				$this->query->whereNull('cover_id');
				break;
		}

		switch (mb_strtolower($this->input['AnnotationExists'])) {
			case 'yes':
				$this->query->where('annotation_exists', true);
				break;
			case 'no':
				$this->query->where('annotation_exists', false);
				break;
		}

		if ($this->input['Formats']) {

			$formats = [];

			$this->input['Formats'] = (array)$this->input['Formats'];

			foreach ($this->input['Formats'] as $format) {
				if (in_array($format, config("litlife.book_allowed_file_extensions"))) {
					$formats[] = $format;
				}
			}

			if (count($formats) > 0) {
				$this->query->whereRaw('jsonb_exists_any("formats", array[' . implode(', ', array_fill(0, count($formats), '?')) . '])', $formats);

				if (!auth()->check() or !auth()->user()->getPermission('access_to_closed_books')) {
					$this->query->where('download_access', true);

					$this->setInputValue('download_access', 'open');
				}
			}
		}

		if ($this->input['rs']) {

			$this->query->whereReadyStatus($this->input['rs']);
		}

		switch ($this->getInputValue('paid_access')) {
			case 'any':
				break;
			case 'paid_only':
				$this->query->paid();
				break;
			case 'only_free':
				$this->query->free();
				break;
		}

		switch ($this->getInputValue('read_access')) {

			case 'any':
				break;
			case 'open':
				$this->query->where('read_access', true);
				break;
			case 'close':
				$this->query->where('read_access', false);
				break;
		}

		switch ($this->getInputValue('download_access')) {

			case 'any':
				break;
			case 'open':
				$this->query->where('download_access', true);
				break;
			case 'close':
				$this->query->where('download_access', false);
				break;
		}

		if ($this->input['si'] == 'only')
			$this->query->where('is_si', true);
		elseif ($this->input['si'] == 'exclude')
			$this->query->where('is_si', false);

		if ($this->input['lp'] == 'only')
			$this->query->where('is_lp', true);
		elseif ($this->input['lp'] == 'exclude')
			$this->query->where('is_lp', false);

		if ($this->input['comments_exists'] == 'yes')
			$this->query->where('comment_count', '>', '0');
		elseif ($this->input['comments_exists'] == 'no')
			$this->query->where('comment_count', '<', '1');

		switch ($this->input['status_of_publication']) {
			case 'published_books_only':
				$this->query->accepted();
				break;
			case 'private_books_only':
				$this->query->private();
				break;
		}

		if ($this->input['kw']) {

			$this->input['kw'] = (array)$this->input['kw'];

			$this->keywords = Keyword::searchFullWord($this->input['kw'])
				->limit(3)
				->get();

            foreach ($this->keywords as $keyword) {
                $this->query->whereHas('book_keywords', function ($query) use ($keyword) {
                    $query->acceptedOrBelongsToAuthUser()->where('keyword_id', $keyword->id);
                });
            }
		}

		if (!empty(intval($this->getInputValue('hide_grouped')))) {
			$this->query->notConnected();
		}

		$this->query->with(['files' => function ($query) {
			$query->select("book_id", "format")->orderBy("format")->distinct();
		}]);

		$this->query->with(['statuses' => function ($query) {
			$query->where('user_id', auth()->id());
		}]);

		$this->query->with(['votes' => function ($query) {
			$query->where('create_user_id', auth()->id());
		}]);

		$this->order();

		//SEOMeta::setDescription(implode(', ', $books->pluck('title')->toArray()));
		/*
				$this->array = [
					'input' => $this->input,
					'books' => $books,
					'genres' => $this->genres,
					'keywords' => $keywords ?? null,
					'exclude_genres' => $this->exclude_genres,
					'view' => $this->view,
					'order_array' => $this->order_array,
					'disabled_filters' => $this->disabled_filters,
					'award' => $award ?? null
				];
				*/

		return $this;
	}

	public function genres()
	{
		if (!empty($this->genres)) {

			$this->query->genre($this->genres->pluck('id')->toArray());

		} elseif ($this->getInputValue('genre')) {

			if (!is_array($this->getInputValue('genre')))
				$ids = collect(preg_split("/[\,\|\ ]+/", $this->getInputValue('genre')));
			else
				$ids = collect($this->getInputValue('genre'));

			$ids = $ids->map(function ($item, $key) {
				return pg_intval(intval($item));
			})->unique();

			if ($ids->isNotEmpty()) {

				$this->genres = Genre::notMain()->whereIn('id', $ids->toArray())->get();

				if ($this->genres->count()) {

					//$this->book_genres_join();
					//$this->query->whereIn("genre_id", $this->genres->pluck('id')->toArray());
					$this->query->genre($this->genres->pluck('id')->toArray());

					//$this->book_genres_joined = true;
				}
			}
		}
	}

	public function andGenres()
	{
		$ids = collect($this->getInputValue('and_genres'))
			->map(function ($item, $key) {
				return pg_intval(intval($item));
			})
			->unique();

		if ($ids->isNotEmpty()) {

			$this->andGenres = Genre::notMain()->whereIn('id', $ids->toArray())->get();

			if ($this->andGenres->count()) {
				$this->query->andGenre($this->andGenres->pluck('id')->toArray());
			}
		}
	}

	public function exclude_genres()
	{
		if ($this->input['exclude_genres']) {
			if (!is_array($this->input['exclude_genres']))
				$ids = collect(preg_split("/[\,\|\ ]+/", $this->input['exclude_genres']));
			else
				$ids = collect($this->input['exclude_genres']);

			$ids = $ids->map(function ($item, $key) {
				return pg_intval(intval($item));
			})->unique();

			if ($ids->isNotEmpty()) {
				$this->exclude_genres = Genre::notMain()->whereIn('id', $ids->toArray())->get();

				if ($this->exclude_genres->count()) {

					//$this->book_genres_join();

					$this->query->withoutGenre($this->exclude_genres->pluck('id')->toArray());
				}
			}
		}
	}

	public function order()
	{
		$this->order_array['rating_avg_down'] = function () {
			$this->query->orderByRatingDesc();
		};

		$this->order_array['rating_avg_up'] = function () {
			$this->query->orderByRatingAsc();
		};

		$this->order_array['date_up'] = function () {
			$this->query->orderBy('books.created_at', 'asc');
		};

		$this->order_array['date_down'] = function () {
			$this->query->orderBy('books.created_at', 'desc');
		};

		$this->order_array['page_count_down'] = function () {
			$this->query->orderBy('page_count', 'desc')
				->orderBy('books.id', 'desc');
		};

		$this->order_array['page_count_up'] = function () {
			$this->query->orderBy('page_count', 'asc')
				->orderBy('books.id', 'desc');
		};

		$this->order_array['comment_count_down'] = function () {
			$this->query->orderBy('comment_count', 'desc')
				->orderBy('books.id', 'desc');
		};

		$this->order_array['comment_count_up'] = function () {
			$this->query->orderBy('comment_count', 'asc')
				->orderBy('books.id', 'desc');
		};

		$this->order_array['user_read_count_down'] = function () {
			$this->query->orderBy('user_read_count', 'desc')
				->orderBy('books.id', 'desc');
		};

		$this->order_array['user_read_count_up'] = function () {
			$this->query->orderBy('user_read_count', 'asc')
				->orderBy('books.id', 'desc');
		};

		$this->order_array['user_read_now_count_down'] = function () {
			$this->query->orderBy('user_read_now_count', 'desc')
				->orderBy('books.id', 'desc');
		};

		$this->order_array['user_read_now_count_up'] = function () {
			$this->query->orderBy('user_read_now_count', 'asc')
				->orderBy('books.id', 'desc');
		};

		$this->order_array['vote_count_down'] = function () {
			$this->query->orderBy('user_vote_count', 'desc')
				->orderBy('books.id', 'desc');
		};

		$this->order_array['vote_count_up'] = function () {
			$this->query->orderBy('user_vote_count', 'asc')
				->orderBy('books.id', 'desc');
		};

		$this->order_array['OnShow_Down'] = function () {
			$this->query->orderStatusChangedDesc();
		};

		$this->order_array['title_asc'] = function () {
			$this->query->orderBy('title', 'asc')
				->orderBy('id', 'asc');
		};

		$this->order_array['pi_year_asc'] = function () {
			$this->query->orderBy('pi_year', 'asc')
				->whereNotNull('pi_year')
				->orderBy('books.id', 'desc');
		};

		$this->order_array['pi_year_desc'] = function () {
			$this->query->orderBy('pi_year', 'desc')
				->whereNotNull('pi_year')
				->orderBy('books.id', 'desc');
		};

		$this->order_array['year_writing_asc'] = function () {
			$this->query->orderBy('year_writing', 'asc')
				->whereNotNull('year_writing')
				->orderBy('books.id', 'desc');
		};

		$this->order_array['year_writing_desc'] = function () {
			$this->query->orderBy('year_writing', 'desc')
				->whereNotNull('year_writing')
				->orderBy('books.id', 'desc');
		};

		$this->order_array['rating_day_desc'] = function () {
			$this->query->orderByRatingDayDesc();
		};

		$this->order_array['rating_week_desc'] = function () {
			$this->query->orderByRatingWeekDesc();
		};

		$this->order_array['rating_month_desc'] = function () {
			$this->query->orderByRatingMonthDesc();
		};

		$this->order_array['rating_quarter_desc'] = function () {
			$this->query->orderByRatingQuarterDesc();
		};

		$this->order_array['rating_year_desc'] = function () {
			$this->query->orderByRatingYearDesc();
		};

		return $this;
	}

	public function setGenres($genres)
	{
		$this->genres = $genres;

		return $this;
	}

	public function setSimplePaginate($set)
	{
		$this->simple_paginate = $set;

		return $this;
	}

	public function book_genres_join()
	{
		if (!$this->book_genres_joined) {
			$this->query->leftJoin('book_genres', 'books.id', '=', 'book_genres.book_id');
			// исключаем повторяющиеся результаты
			$this->query->groupBy('books.id', 'user_books.user_id', 'user_books.book_id', 'user_books.created_at');

			$this->book_genres_joined = true;
		}
	}

	public function saveSettings()
	{
		$this->saveSettings = true;

		foreach (auth()->user()->booksSearchSettings()->get() as $setting) {
			$this->setDefaultInputValue($setting->name, $setting->value);
		}

		return $this;
	}

	public function isSaveSetting()
	{
		return $this->saveSettings;
	}
}
