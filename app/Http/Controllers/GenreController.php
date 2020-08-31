<?php

namespace App\Http\Controllers;

use App\Book;
use App\Genre;
use App\Http\Requests\StoreGenre;
use App\Library\BookSearchResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class GenreController extends Controller
{
	/**
	 * Список жанров
	 *
	 * @return View
	 */
	public function index()
	{
		$genres = Genre::main()
			->with(['childGenres' => function ($query) {
				$query->orderBy('name', 'asc');
			}])
			->orderBy('book_count', 'desc')
			->get();

		return view('genre.index', compact('genres'));
	}

	/**
	 * Форма создания жанра
	 *
	 * @return View
	 * @throws
	 */
	public function create()
	{
		$this->authorize('create', Genre::class);

		return view('genre.create');
	}

	/**
	 * Сохранение жанра
	 *
	 * @param StoreGenre $request
	 * @return Response
	 * @throws
	 */
	public function store(StoreGenre $request)
	{
		$this->authorize('create', Genre::class);

		$genreGroup = Genre::findOrFail($request->genre_group_id);

		if (!$genreGroup->isMain()) {
			return back()
				->withInput(request()->all())
				->withErrors([__('genre.genre_group_id_must_be_a_genre_group')]);
		}

		$genre = new Genre;
		$genre->fill($request->all());
		$genre->save();

		return redirect()
			->route('genres');
	}

	/**
	 * Форма редактирования
	 *
	 * @param Genre $genre
	 * @return View
	 * @throws
	 */
	public function edit(Genre $genre)
	{
		$this->authorize('update', $genre);

		return view('genre.edit', compact('genre'));
	}

	/**
	 * Сохранение отредактированного
	 *
	 * @param StoreGenre $request
	 * @param Genre $genre
	 * @return Response
	 * @throws
	 */
	public function update(StoreGenre $request, Genre $genre)
	{
		$this->authorize('update', $genre);

		$genreGroup = Genre::findOrFail($request->genre_group_id);

		if (!$genreGroup->isMain()) {
			return back()
				->withInput(request()->all())
				->withErrors([__('genre.genre_group_id_must_be_a_genre_group')]);
		}

		$genre->fill($request->all());
		$genre->save();

		return redirect()
			->route('genres');
	}

	/**
	 * Вывод списка книг, которые принадлежат этому жанру
	 *
	 * @param Genre $genre
	 * @return Response
	 */
	public function show(Request $request, Genre $genre)
	{
		$builder = Book::acceptedOrBelongsToAuthUser();

		$resource = (new BookSearchResource($request, $builder))
			->setDefaultInputValue('hide_grouped', '1')
			->setDefaultInputValue('read_access', 'open')
			->defaultSorting('rating_week_desc')
			->disableFilter('genres');

		if ($genre->isMain()) {
			$resource->setGenres($genre->childGenres);
		} else {
			$resource->setGenres(collect([$genre]));
		}

		if (auth()->check()) {
			$resource->saveSettings();
		}

		return $resource->view();
	}

	/**
	 * Поиск жанра js
	 *
	 * @param Request $request
	 * @return array
	 */
	public function search(Request $request)
	{
		$q = $request->input('q');

		$genres = Genre::when($q, function ($query) use ($q) {
			$query->where(function ($query) use ($q) {
				$query->search($q)
					->orWhere('id', intval($q));
			});
		})
			->with('group')
			->notMain()
			->orderBy('book_count', 'desc')
			->orderBy('id', 'asc')
			->simplePaginate();

		return $genres;
	}

	/**
	 * Вывод всех жанров для плагина select2
	 *
	 * @return array
	 */
	public function allForSelect2(Request $request)
	{
		$genres = [];

		foreach (Genre::main()->orderBy('id')->with('childGenres')->get() ?? [] as $group) {

			foreach ($group->genres as $genre) {
				$genres[] = ['id' => $genre->id, 'text' => $genre->name];
			}
		}

		return $genres;
	}

	/**
	 * Удаление жанра
	 *
	 * @param int $id
	 */
	public function destroy($id)
	{
		//
	}

	public function selectList()
	{
		$genres = Genre::main()
			->with(['childGenres' => function ($query) {
				$query->orderBy('name', 'asc');
			}])
			->orderBy('book_count', 'desc')
			->get();

		if (request()->ajax())
			return view('genre.select_list', compact('genres'))->renderSections()['content'];

		return view('genre.select_list', compact('genres'));
	}
}
