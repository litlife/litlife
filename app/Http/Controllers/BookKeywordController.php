<?php

namespace App\Http\Controllers;

use App\Book;
use App\BookKeyword;
use App\BookKeywordVote;
use App\Jobs\Book\BookAddKeywordsJob;
use App\Keyword;
use Coderello\SharedData\Facades\SharedData;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BookKeywordController extends Controller
{
	/**
	 * Список ключевых слов у книги
	 *
	 * @param Book $book
	 * @return View
	 * @throws
	 */
	public function index(Book $book)
	{
		$this->authorize('addKeywords', $book);

		SharedData::put(['book_id' => $book->id]);

        if ($book->isInGroup() and $book->isNotMainInGroup() and !empty($book->mainBook))
            $mainBook = $book->mainBook;
        else
            $mainBook = $book;

		$keywords = $mainBook->book_keywords()
			->whereHas('keyword')
			->with(['create_user', 'keyword'])
			->latest()
			->get();

		return view('book.keyword.index', compact('book', 'keywords'));
	}

	/**
	 * Сохранение
	 *
	 * @param Book $book
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
     * @throws
	 */
	public function store(Book $book, Request $request)
	{
		$this->authorize('addKeywords', $book);

		$this->validate($request, ['keywords' => 'required|array']);

		BookAddKeywordsJob::dispatch($book, $request->keywords);

		BookKeyword::flushCachedOnModerationCount();

		return back();
	}

	/**
	 * Удаление
	 *
	 * @param $book
     * @param int $id
	 * @return void
	 * @throws
	 */
	public function destroy($book, $id)
	{
		$book_keyword = BookKeyword::withUnchecked()
			->findOrFail($id);

		$this->authorize('delete', $book_keyword);

		$book_keyword->forceDelete();

		BookKeyword::flushCachedOnModerationCount();
	}

	/**
	 * Поиск js
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\Pagination\Paginator
     */
	public function search(Request $request)
	{
		/*
		$items = BookKeyword::selectRaw('COUNT("keyword_id") as count, keyword_id, keyword_id as "id"')
			->whereHas('keyword', function ($query) use ($request) {
				$query->searchPartWord($request->input('q'));
			})->groupBy('keyword_id')
			->with('keyword')
			->orderBy("count", "desc")
			->simplePaginate();
		*/

		$items = Keyword::searchPartWord($request->input('q'))
			->orderBy("count", "desc")
			->simplePaginate();

		return $items;
	}

	/**
	 * Голосование за ключевое слово
	 *
	 * @param Request $request
	 * @param Book $book
	 * @param int $id
	 * @param int $vote
	 * @return array
	 * @throws
	 */
	public function vote(Request $request, Book $book, $id, $vote)
	{
		$this->authorize('vote', BookKeyword::class);

		$book_keyword = $book->book_keywords()->findOrFail($id);

		$keyword_vote = $book_keyword->votes()->where('create_user_id', auth()->id())->first();

		if (empty($keyword_vote)) {
			$keyword_vote = new BookKeywordVote;
			$keyword_vote->book_keyword_id = $book_keyword->id;
			$keyword_vote->vote = $vote;
		} else {

			if ((($keyword_vote->vote > 0) and ($vote > 0)) or (($keyword_vote->vote < 0) and ($vote < 0))) {
				$keyword_vote->vote = 0;
			} else {
				$keyword_vote->vote = $vote;
			}
		}
		$keyword_vote->save();

		return $keyword_vote;
	}

	/**
	 * Ключевые слова на проверке
	 *
	 * @return View
	 */
	public function onModeration()
	{
		$keywords = BookKeyword::sentOnReview()
			->joinKeywords()
			->select(['book_keywords.*', 'keywords.text'])
			->with('create_user', 'keyword')
			->with(['book' => function ($query) {
				$query->any();
			}])
			->latest()
			->simplePaginate();

		return view('book.keyword.on_moderation', compact('keywords'));
	}

	/**
	 * Одобрить ключевое слово
	 *
	 * @param Book $book
	 * @param int $id
	 * @return void
	 * @throws
	 */
	public function approve(Book $book, int $id)
	{
		$book_keyword = $book->book_keywords()->sentOnReview()->findOrFail($id);

		$this->authorize('approve', $book_keyword);

		$book_keyword->statusAccepted();
		$book_keyword->save();

		BookKeyword::flushCachedOnModerationCount();
	}
}
