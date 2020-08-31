<?php

namespace App\Http\Controllers;

use App\Book;
use App\Events\BookViewed;
use App\Library\BookSqlite;
use App\Library\Old\xsBookPath;
use Artesaos\SEOTools\Facades\SEOMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class BookReadOldController extends Controller
{
	/**
	 * Вывод страниц онлайн чтения в старом формате
	 *
	 * @param Book $book
	 * @param
	 * @return View
	 */
	function show(Book $book, Request $request)
	{
		if ($book->trashed())
			abort(404);

		$this->authorize('read', $book);

		$db_path = $book->getBookPath();

		if (!file_exists($db_path))
			abort(404);

		$sqlite = new BookSqlite();
		$sqlite->connect($db_path);

		$currentPage = LengthAwarePaginator::resolveCurrentPage();

		$page = (function () use ($sqlite, $currentPage, $book) {
			$text = $sqlite->pageContent($currentPage);

			if (empty($text))
				return null;

			$text = preg_replace_callback('|\<\!\-\-\[litru_binary\](.*)\[\/litru_binary\]\-\-\>|Uis', function ($array) use ($book, $sqlite) {

				$name = $array[1];

				$binary = $sqlite->binaryContentByName($name);

				if (!empty($binary)) {
					$param = unserialize($binary['br_param']);

					$s = '<img';

					if (!empty($param['w']))
						$s .= ' width="' . $param['w'] . '"';
					if (!empty($param['h']))
						$s .= ' height="' . $param['h'] . '"';

					$s .= ' alt="' . $book->title . ' ' . $binary['br_name'] . '" src="' .
						route('books.old.image', ['book' => $book->id, 'name' => $binary['br_name']]) . '">';

					return $s;
				}

			}, $text);

			return $text;
		})();

		if (empty($page))
			abort(404);

		$sections_count = $sqlite->sectionsCount();

		$sections = $sqlite->sections();

		$pages_count = $sqlite->pagesCount();

		$pages = new LengthAwarePaginator(['0' => $page], $pages_count, $request->per_page ?? 1,
			$currentPage, ['path' => $request->url(), 'query' => $request->query()]);

		if (auth()->check())
			$book->rememberPageForUser(auth()->user(), $pages->currentPage());

		$text = $pages->first();

		$description = __('page.read_page_online', ['page' => $pages->currentPage()]) . '. ';

		if (!empty($text)) {
			$description .= str_replace('</p>', ' ', $text);
			SEOMeta::setDescription(mb_substr(strip_tags($description), 0, 500));
		}

		$array = split_text_with_tags_on_percent($text, rand(30, 60));

		event(new BookViewed($book));

		return view('book.page.show', [
			'book' => $book,
			'pages' => $pages,
			'text' => $text,
			'before' => $array['before'] ?? '',
			'after' => $array['after'] ?? '',
			'sections_count' => $sections_count,
			'sections' => $sections ?? [],
			'characters_count' => mb_strlen($text)
		]);
	}

	/**
	 * Вывод изображений в старом формате
	 *
	 * @param Book $book
	 * @param string $name
	 * @return Response
	 */
	function binary(Book $book, $name)
	{
		if ($book->trashed())
			abort(404);

		$db_path = xsBookPath::GetPathToSqliteDB($book->id);

		if (!file_exists($db_path))
			return abort(404);

		$sqlite = new BookSqlite();
		$sqlite->connect($db_path);

		$binary = $sqlite->binaryContentByName($name);

		if (empty($binary))
			return abort(404);

		return response($binary['br_content'])
			->setLastModified(Carbon::createFromTimestamp($binary['br_edit_time']))
			->setExpires(now()->addYears(10))
			->withHeaders([
				'Content-Type' => $binary['br_mime_type'],
				'cache-control' => 'public, max-age=31536000'
			]);
	}
}