<?php

namespace App\Http\Controllers;

use App\Book;
use App\Events\BookViewed;
use App\Http\Requests\StoreSection;
use App\Jobs\Book\UpdateBookNotesCount;
use App\Jobs\Book\UpdateBookSectionsCount;
use App\Library\BookSqlite;
use App\Section;
use Artesaos\SEOTools\Facades\SEOMeta;
use DOMDocument;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SectionController extends Controller
{
	/**
	 * Список
	 *
	 * @param Request $request
	 * @param Book $book
	 * @return View
	 * @throws
	 */
	public function index(Request $request, Book $book)
	{
		$this->authorize('view_section_list', $book);

		js_put('book', $book);

		$book->load(['authors.managers', 'parse']);

		if ($book->isPagesNewFormat()) {
			$sections = Section::scoped(['book_id' => $book->id, 'type' => $this->getType()])
				->when(!$book->isAuthUserCreator(), function ($query) {
					$query->accepted();
				})
				->defaultOrder()
				->get();

			foreach ($sections as $section)
				$section->setRelation('book', $book);

			$sections = $sections->toTree();

			if (auth()->check()) {
				$purchase = $book->purchases
					->where('buyer_user_id', auth()->id())
					->first();
			}

			return view('section.index', [
				'sections' => $sections,
				'book' => $book,
				'type' => $this->getType(),
				'purchase' => $purchase ?? null
			]);
		} else {
			$db_path = $book->getBookPath();

			if (!file_exists($db_path))
				abort(404);

			$sqlite = new BookSqlite();
			$sqlite->connect($db_path);

			$sections_count = $sqlite->sectionsCount();

			$sections = $sqlite->sections();

			return view('book.page.sections', [
				'sections' => $sections,
				'sections_count' => $sections_count,
				'book' => $book
			]);
		}
	}

	protected function getType()
	{
		if ($this instanceof NoteController)
			$type = 'note';
		elseif ($this instanceof SectionController)
			$type = 'section';

		return $type;
	}

	/**
	 * Форма создания
	 *
	 * @param Request $request
	 * @param Book $book
	 * @return View
	 * @throws
	 */
	public function create(Request $request, Book $book)
	{
		$this->authorize('create_section', $book);

		if (($this->getType() == 'section') and ($request->parent)) {
			$parent = Section::scoped(['book_id' => $book->id, 'type' => $this->getType()])
				->findInnerIdOrFail($request->parent);

			$pathSections = $parent->ancestors;
		}

		$section = new Section;
		$section->type = $this->getType();
		$section->setRelation('book', $book);

		if ($section->isSection())
			$action = route('books.sections.store', $book);
		elseif ($section->isNote())
			$action = route('books.notes.store', $book);

		return view('section.create', [
			'action' => $action,
			'book' => $book,
			'section' => $section,
			'parent' => $parent ?? null,
			'pathSections' => $pathSections ?? null
		]);
	}

	/**
	 * Сохранение
	 *
	 * @param StoreSection $request
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function store(StoreSection $request, Book $book)
	{
		$this->authorize('create_section', $book);

		$section = new Section;
		$section->scoped(['book_id' => $book->id, 'type' => $this->getType()]);
		$section->type = $this->getType();
		$section->fill($request->all());

		if ($section->character_count > config('litlife.max_section_characters_count')) {
			return redirect()->back()
				->withErrors(['content' => __('validation.max.string', [
					'max' => config('litlife.max_section_characters_count'),
					'attribute' => __('section.content')
				])])
				->withInput();
		}

		$section->book_id = $book->id;

		if (!empty($request->input('status')) and auth()->user()->can('use_draft', $section))
			$section->status = $request->input('status');
		else
			$section->statusAccepted();

		if (($this->getType() == 'section') and ($request->parent)) {
			$parent = Section::scoped(['book_id' => $book->id, 'type' => $this->getType()])
				->findInnerIdOrFail($request->parent);

			// проверяем секция принадлжит этой книге

			if ($parent->book_id != $book->id) {
				abort(403);
			}

			$section->appendToNode($parent)->save();
		} else {
			$section->save();
		}

		if ($this->getType() == 'section') {
			UpdateBookSectionsCount::dispatch($book);
		} else if ($this->getType() == 'note')
			UpdateBookNotesCount::dispatch($book);

		$book->user_edited_at = now();
		$book->edit_user_id = auth()->id();
		$book->changed();
		$book->save();

		$book->updatePageNumbers();

		if ($this->getType() == 'section')
			return redirect()->route('books.sections.index', ['book' => $book->id]);
		elseif ($this->getType() == 'note')
			return redirect()->route('books.notes.index', ['book' => $book->id]);
	}

	/**
	 * Отображение главы
	 *
	 * @param Book $book
	 * @param int $section
	 * @return View
	 * @throws
	 */
	public function show(Request $request, Book $book, $section)
	{
		if (!is_object($section))
			$section = $book->sections()->findInnerIdOrFail($section);

		$section->setRelation('book', $book);

		try {
			$this->authorize('view', $section);
		} catch (AuthorizationException $exception) {
			if ($exception->getMessage() == __('book.paid_part_of_book')) {
				return redirect()
					->route('books.purchase', $book)
					->with(['info' => __('book.paid_part_of_book')]);
			} else {
				throw $exception;
			}
		}

		/*
				if ((auth()->check() ? auth()->user() : new User())->cant('view', $section))
					return abort(403, __('book.you_dont_have_access'));
		*/
		if ($this->getType() != $section->type) {
			if ($section->isNote())
				return redirect()->route('books.notes.show', ['book' => $book->id, 'note' => $section->inner_id]);
			elseif ($section->isSection())
				return redirect()->route('books.sections.show', ['book' => $book->id, 'section' => $section->inner_id]);
		}

		$pages = $section->pages()
			->orderBy('page', 'asc')
			->paginate(1);

		$page = $pages->first();

		if (empty($page) and $pages->currentPage() > 1)
			return response()->view('section.not_found', [
				'page' => $page,
				'section' => $section,
				'book' => $book
			])->setStatusCode(404);

		if ($page) {
			$page->setRelation('book', $book);
			$page->setRelation('section', $section);
		}

		if ($this->getType() == 'section' and auth()->check()) {
			$book->rememberPageForUser(auth()->user(), $page->page ?? $pages->currentPage(), $section->inner_id);
		}

		event(new BookViewed($book));

		$description = '';

		if ($pages->first()) {

			$description .= __('page.read_section_page_online', ['page' => $pages->currentPage()]) . '. ';

			$description .= str_replace('</p>', ' ', $pages->first()->content);

			SEOMeta::setDescription(mb_substr(strip_tags($description), 0, 200));
		}

		// последняя страница
		if ($pages->currentPage() == $pages->lastPage()) {
			$nextSection = Section::where('book_id', $book->id)->where('type', $this->getType())
				->when(!$book->isAuthUserCreator(), function ($query) {
					$query->accepted();
				})
				->where('_lft', '>', $section->_lft)
				->orderBy('_lft', 'asc')
				->first();

			if (!empty($nextSection)) {
				$nextSectionFirstPageUrl = route('books.sections.show', [
					'book' => $book,
					'section' => $nextSection->inner_id,
					'page' => 1
				]);
			}
		}

		// первая страница
		if ($pages->currentPage() == 1) {
			$prevSection = Section::where('book_id', $book->id)->where('type', $this->getType())
				->when(!$book->isAuthUserCreator(), function ($query) {
					$query->accepted();
				})
				->where('_lft', '<', $section->_lft)
				->orderBy('_lft', 'desc')
				->first();

			if (!empty($prevSection)) {
				$prevSectionLastPageNumber = $prevSection->pages()
					->orderBy('page', 'asc')
					->max('page');

				$prevSectionLastPageUrl = route('books.sections.show', [
					'book' => $book,
					'section' => $prevSection->inner_id,
					'page' => $prevSectionLastPageNumber
				]);
			}
		}

		if (!empty($page->book_page)) {
			$book_pages = new LengthAwarePaginator(['0' => $page], $book->page_count, 1,
				$page->book_page, [
					'path' => route('books.pages', ['book' => $book]),
					'query' => $request->query()
				]);
		}

		$array = [
			'book_pages' => $book_pages ?? null,
			'pages' => $pages,
			'section' => $section,
			'book' => $book,
			'pathSections' => $section->ancestors,
			'nextSection' => $nextSection ?? null,
			'prevSection' => $prevSection ?? null,
			'prevSectionLastPageUrl' => $prevSectionLastPageUrl ?? null,
			'nextSectionFirstPageUrl' => $nextSectionFirstPageUrl ?? null
		];

		if (request()->ajax())
			return view($this->getType() . '.text', $array)->renderSections()['text'];
		else
			return response()->view($this->getType() . '.text', $array, $section->trashed() ? 404 : 200);
	}

	/**
	 * Форма редактирования
	 *
	 * @param Book $book
	 * @param int $section
	 * @return View
	 * @throws
	 */
	public function edit(Book $book, $section)
	{
		if (!is_object($section))
			$section = $book->sections()->findInnerIdOrFail($section);

		$book->load('authors.managers');

		$section->setRelation('book', $book);

		$this->authorize('update', $section);

		$pathSections = $section->ancestors;

		//dd($section);

		return view($this->getType() . '/edit', [
			'section' => $section,
			'book' => $book,
			'pathSections' => $pathSections
		]);
	}

	/**
	 * Сохранение редактированого
	 *
	 * @param StoreSection $request
	 * @param Book $book
	 * @param int $section
	 * @return Response
	 * @throws
	 */
	public function update(StoreSection $request, Book $book, $section)
	{
		if (!is_object($section))
			$section = $book->sections()->findInnerIdOrFail($section);

		$section->setRelation('book', $book);

		$this->authorize('update', $section);

		if (preg_match("/u\-section\-break/iu", $request->input('content'))) {

			$content = '';
			$n = 1;

			// <hr class="u-section-break" />

			$dom = new DOMDocument();
			$dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $request->input('content'));

			$bodyNode = $dom->getElementsByTagName('body')->item(0);

			foreach ($bodyNode->childNodes as $node) {
				if (($node->nodeType == XML_ELEMENT_NODE) and ($node->hasAttribute("class")) and ($node->getAttribute("class") == "u-section-break")) {
					if ($n == 1) {
						$section->title = $request->title;
						$section->content = $content;
						$section->user_edited_at = now();
						$section->save();
						$section->fresh();
					} else {
						$new_section = new Section;
						$new_section->scoped(['book_id' => $book->id, 'type' => $this->getType()]);
						$new_section->content = $content;

						if (empty($new_section->title))
							$new_section->title = $content;

						$new_section->book_id = $book->id;
						$new_section->user_edited_at = now();
						$new_section->afterNode($section)->save();
						$section = $new_section;
					}

					$n++;
					$content = '';
				} else {
					$content .= trim($dom->saveHTML($node));
				}
			}

			$new_section = new Section;
			$new_section->scoped(['book_id' => $book->id, 'type' => $this->getType()]);
			$new_section->content = $content;

			if (empty($new_section->title))
				$new_section->title = $content;

			$new_section->book_id = $book->id;
			$new_section->user_edited_at = now();
			$new_section->afterNode($section)->save();
			$section = $new_section;

			if ($this->getType() == 'section') {
				UpdateBookSectionsCount::dispatch($book);
			} else if ($this->getType() == 'note')
				UpdateBookNotesCount::dispatch($book);

		} else {
			$section->fill($request->all());

			if ($section->character_count > config('litlife.max_section_characters_count')) {
				return redirect()->back()
					->withErrors(['content' => __('validation.max.string', [
						'max' => config('litlife.max_section_characters_count'),
						'attribute' => __('section.content')
					])])
					->withInput();
			}

			$section->user_edited_at = now();

			if (!empty($request->input('status')) and auth()->user()->can('use_draft', $section))
				$section->status = $request->input('status');
			else
				$section->statusAccepted();

			$section->save();
		}

		$book->user_edited_at = now();
		$book->edit_user_id = auth()->id();
		$book->changed();
		$book->save();

		$book->updatePageNumbers();

		return redirect()
			->route('books.sections.edit', ['book' => $book, 'section' => $section->inner_id])
			->with(['success' => __('common.data_saved')]);
	}

	/**
	 * Удаление
	 *
	 * @param Book $book
	 * @param int $section
	 * @return Response
	 * @throws
	 */
	public function destroy(Book $book, $section)
	{
		if ($section->trashed()) {
			$this->authorize('restore', $section);
			$section->restore();
		} else {
			$this->authorize('delete', $section);
			$section->delete();
		}

		if ($this->getType() == 'section')
			UpdateBookSectionsCount::dispatch($book);
		else if ($this->getType() == 'note')
			UpdateBookNotesCount::dispatch($book);

		$book->user_edited_at = now();
		$book->edit_user_id = auth()->id();
		$book->changed();
		$book->save();

		$book->updatePageNumbers();

		if (request()->ajax())
			return $section;

		return redirect()->route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]);
	}

	/**
	 * Сохранение расположения
	 *
	 * @param Request $request
	 * @param Book $book
	 * @return array
	 * @throws
	 */
	public function savePosition(Request $request, Book $book)
	{
		$this->authorize('save_sections_position', $book);

		$hierarchy = $request->hierarchy;

		if (isset($hierarchy)) {

			Section::scoped(['book_id' => $book->id, 'type' => $this->getType()])
				->with('book')
				->rebuildTree($hierarchy);

			$book->user_edited_at = now();
			$book->edit_user_id = auth()->id();
			$book->changed();
			$book->save();

			$book->updatePageNumbers();

			return $hierarchy;
		}
	}

	/**
	 * Отображение глав и сносок
	 *
	 * @param Request $request
	 * @param Book $book
	 * @return Collection $all
	 * @throws
	 */
	public function loadList(Request $request, Book $book)
	{
		$this->authorize('view_section_list', $book);

		$FlatTree = Section::scoped(['book_id' => $book->id, 'type' => 'note'])
			->select('id', 'inner_id', 'title', 'book_id')
			->with('pages')
			->defaultOrder()
			->get()
			->toFlatTree();

		$FlatTree2 = Section::scoped(['book_id' => $book->id, 'type' => 'section'])
			->select('id', 'inner_id', 'title', 'book_id')
			->defaultOrder()
			->with('pages')
			->get()
			->toFlatTree();

		$FlatTree->load(['pages' => function ($query) {
			$query->select('id', 'section_id', 'page', 'html_tags_ids');
		}]);

		$FlatTree2->load(['pages' => function ($query) {
			$query->select('id', 'section_id', 'page', 'html_tags_ids');
		}]);

		$collection = new Collection;
		$all = $collection->merge($FlatTree)->merge($FlatTree2);

		return $all;
	}

	/**
	 * Переместить выбранные главы в сноски
	 *
	 * @param Book $book
	 * @param Request $request
	 * @return array
	 * @throws
	 */
	public function moveToNote(Book $book, Request $request)
	{
		$this->authorize('move_sections_to_notes', $book);

		$sections_ids = (array)$request->input('ids');

		if (!empty($sections_ids)) {

			Section::where('book_id', $book->id)
				->whereIn('id', $sections_ids)
				->update(['type' => 'note', 'parent_id' => null]);

			Section::scoped(['book_id' => $book->id, 'type' => 'section'])->fixTree();
			Section::scoped(['book_id' => $book->id, 'type' => 'note'])->fixTree();
		}

		UpdateBookSectionsCount::dispatch($book);
		UpdateBookNotesCount::dispatch($book);

		$book->user_edited_at = now();
		$book->edit_user_id = auth()->id();
		$book->changed();
		$book->save();

		$book->updatePageNumbers();

		//$request->input('sectionIds')
		return ['ids' => $sections_ids];
	}

	/**
	 * Переместить сноски в главы
	 *
	 * @param Book $book
	 * @param Request $request
	 * @return array
	 * @throws
	 */
	public function moveToChapters(Book $book, Request $request)
	{
		$this->authorize('move_sections_to_notes', $book);

		$sections_ids = (array)$request->input('ids');

		if (!empty($sections_ids)) {

			Section::where('book_id', $book->id)
				->whereIn('id', $sections_ids)
				->update(['type' => 'section', 'parent_id' => null]);

			Section::scoped(['book_id' => $book->id, 'type' => 'section'])->fixTree();
			Section::scoped(['book_id' => $book->id, 'type' => 'note'])->fixTree();
		}

		UpdateBookSectionsCount::dispatch($book);
		UpdateBookNotesCount::dispatch($book);

		$book->user_edited_at = now();
		$book->edit_user_id = auth()->id();
		$book->changed();
		$book->save();

		$book->updatePageNumbers();

		//$request->input('sectionIds')
		return ['ids' => $sections_ids];
	}

	public function page(Request $request, Book $book)
	{
		$number = pg_smallintval($request->page);

		$page = $book->pages()
			->where('book_page', $number)
			->has('section')
			->firstOrFail();

		$section = $page->section;

		if (empty($section))
			abort(404);

		$page_number = $page->page;

		if ($page_number < 2)
			$page_number = null;

		return redirect()
			->route('books.sections.show', [
				'book' => $book,
				'section' => $section->inner_id,
				'page' => $page_number
			]);
	}

	/**
	 * Список глав который открывается в диалоговом окне
	 *
	 * @param Request $request
	 * @param Book $book
	 * @return View
	 * @throws
	 */
	public function listGoToChapter(Request $request, Book $book)
	{
		$this->authorize('view_section_list', $book);

		if ($book->isPagesNewFormat()) {
			$sections = Section::scoped(['book_id' => $book->id, 'type' => 'section'])
				->when(!$book->isAuthUserCreator(), function ($query) {
					$query->accepted();
				})
				->with(['pages' => function ($query) {
					$query->where('page', '1');
				}])
				->defaultOrder()
				->withDepth()
				->get()
				->toTree();

			return view('book.chapter.list_go_to', [
				'sections' => $sections,
				'book' => $book
			]);
		} else {
			$db_path = $book->getBookPath();

			if (!file_exists($db_path))
				abort(404);

			$sqlite = new BookSqlite();
			$sqlite->connect($db_path);

			$sections_count = $sqlite->sectionsCount();

			$sections = $sqlite->sections();

			return view('book.chapter.old_page_format_list_go_to', [
				'sections' => $sections,
				'sections_count' => $sections_count,
				'book' => $book
			]);
		}
	}
}
