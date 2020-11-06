<?php

namespace App\Http\Controllers;

use App\Author;
use App\AuthorBiography;
use App\AuthorGroup;
use App\AuthorParsedData;
use App\AuthorStatus;
use App\BookVote;
use App\Comment;
use App\Enums\AuthorEnum;
use App\Events\AuthorViewed;
use App\Forum;
use App\Http\Requests\StoreAuthor;
use App\Library\CommentSearchResource;
use App\Manager;
use App\Notifications\AuthorPageNeedsToBeVerifiedNotification;
use App\UserAuthor;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Carbon\Carbon;
use Coderello\SharedData\Facades\SharedData;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthorController extends Controller
{
	/**
	 * Форма создания нового автора
	 *
	 * @return View
	 * @throws
	 */
	public function create()
	{
		$this->authorize('create', Author::class);

		return view('author.create');
	}

	/**
	 * Сохранить автора
	 *
	 * @param StoreAuthor $request
	 * @return Response
	 * @throws
	 */
	public function store(StoreAuthor $request)
	{
		$this->authorize('create', Author::class);

		$author = new Author;
		$author->fill($request->all());
		$author->save();

		if (!empty($request->biography)) {

			$author->biography()->updateOrCreate([],
				[
					'text' => $request->biography,
					'author_id' => $author->id
				]
			);
		}

		$author->save();

		if ($author->create_user->isNameMatchesAuthorName($author)) {
			$author->create_user->notify(new AuthorPageNeedsToBeVerifiedNotification($author));
		}

		activity()
			->performedOn($author)
			->log('created');

		return redirect()->route('authors.show', $author)->with([
			'sisyphus_ok' => request()->sisyphus ?? null
		]);
	}

	/**
	 * Отображение страницы автора
	 *
	 * @param Author $author
	 * @return View
	 */

	public function show(Author $author)
	{
		SharedData::put(['author_id' => $author->id]);

		$author->load(['likes' => function ($query) {
			$query->where('create_user_id', auth()->id());
		}]);

		$author->load(['library_users' => function ($query) {
			$query->where('user_id', auth()->id());
		}]);

		$author->load(['users_read_statuses' => function ($query) {
			$query->where('user_id', auth()->id());
		}]);

		$author->load(['group.authors' => function ($query) use ($author) {
			$query->where('id', '!=', $author->id);
		}]);

		$managers = $author->managers;

		if ($managers->count()) {

			$manager_author = $managers->where('character', 'author')->first();

			if (isset($manager_author)) {
				$author_user = $manager_author->user;
			}
		}

		event(new AuthorViewed($author));

		$author->load(['any_books' => function ($query) use ($author) {
			$query->acceptedOrBelongsToAuthUser()
				->orderBy('title', 'asc');
		}]);

		$author->any_books->load(['genres', 'sequences', 'statuses' => function ($query) {
			$query->where('user_id', auth()->id());
		}]);

		$author->setRelation('written_books', $author->getAnyBooksByType(AuthorEnum::Writer));
		$author->setRelation('translated_books', $author->getAnyBooksByType(AuthorEnum::Translator));
		$author->setRelation('edited_books', $author->getAnyBooksByType(AuthorEnum::Editor));
		$author->setRelation('illustrated_books', $author->getAnyBooksByType(AuthorEnum::Illustrator));
		$author->setRelation('compiled_books', $author->getAnyBooksByType(AuthorEnum::Compiler));

		$written_books = $author->written_books->filter(function ($item, $key) {
			if ($item->isInGroup()) {
				if ($item->isMainInGroup())
					return $item;
			} else {
				return $item;
			}
		});

		$books_count = $written_books->count() +
			$author->translated_books->count() +
			$author->edited_books->count() +
			$author->illustrated_books->count() +
			$author->compiled_books->count();

		$array = [
			'books_count' => $books_count,
			'written_books' => $written_books,
			'translated_books' => $author->translated_books,
			'edited_books' => $author->edited_books,
			'illustrated_books' => $author->illustrated_books,
			'compiled_books' => $author->compiled_books,
			'author' => $author,
			'user_read_status' => $author->users_read_statuses->first(),
			'author_user' => isset($author_user) ? $author_user : null,
			'managers' => $managers
		];

		if (isActiveRoute('authors.show')) {
			if (request()->ajax())
				return view('author.books', $array);
		}

		$description = $author->getShareDescription();

		if (!empty($description)) {
			OpenGraph::setDescription($description);
			TwitterCard::setDescription($description);
			SEOMeta::setDescription($description);
		}

		OpenGraph::setType('profile')
			->setTitle($author->name)
			->setProfile([
				'first_name' => $author->first_name,
				'last_name' => $author->last_name,
				'username' => $author->nickname,
				'gender' => $author->gender
			]);

		if (!empty($author->photo)) {
			OpenGraph::addImage($author->photo->url,
				[
					'width' => $author->photo->getWidth(),
					'height' => $author->photo->getHeight()
				]);

			TwitterCard::setImage($author->photo->fullUrlMaxSize(900, 900));
		}

		if (!empty($author->lang))
			OpenGraph::addProperty('locale', strtolower($author->lang) . '_' . strtoupper($author->lang));

		TwitterCard::setTitle($author->name);

		if (($author->isPrivate()) and (auth()->id() != $author->create_user_id))
			return response()->view('author.show_private', $array, 403);
		else {
			return response()->view('author.show', $array, $author->trashed() ? 404 : 200);
		}
	}

	/**
	 * Форма редактирования страницы авора
	 *
	 * @param Author $author
	 * @return View
	 * @throws
	 */
	public function edit(Author $author)
	{
		$this->authorize('update', $author);

		return view('author.edit', compact('author'));
	}

	/**
	 * Сохранение автора
	 *
	 * @param StoreAuthor $request
	 * @param Author $author
	 * @return Response
	 * @throws
	 */
	public function update(StoreAuthor $request, Author $author)
	{
		$this->authorize('update', $author);

		$author->fill($request->all());
		$author->user_edited_at = now();
		$author->edit_user_id = auth()->id();
		$author->save();

		if (!empty($request->biography)) {
			$biography = $author->biography()->withTrashed()->first();

			if (empty($biography))
				$biography = new AuthorBiography;
			elseif ($biography->trashed())
				$biography->restore();

			$biography->text = $request->biography;
			$biography->user_edited_at = now();
			$biography->edit_user_id = auth()->id();
			$biography->author_id = $author->id;
			$biography->save();

			$author->biography()->associate($biography);
		} else {
			$author->biography()->delete();
		}

		$author->save();

		activity()
			->performedOn($author)
			->log('updated');

		return redirect()
			->route('authors.edit', $author)
			->with(['success' => __('author.description_was_saved_successfully')]);
	}

	/**
	 * Удалить или восстановить автора через js
	 *
	 * @param Author $author
	 * @return object
	 * @throws
	 */
	public function destroy(Author $author)
	{
		if ($author->trashed()) {
			$this->authorize('restore', $author);

			$author->restore();

			activity()
				->performedOn($author)
				->log('restored');
		} else {
			$this->authorize('delete', $author);

			$author->delete();

			activity()
				->performedOn($author)
				->log('deleted');
		}

		return $author;
	}

	/**
	 * Удалить или восстановить автора
	 *
	 * @param Author $author
	 * @return Response
	 * @throws
	 */
	public function delete(Author $author)
	{
		if ($author->trashed()) {
			$this->authorize('restore', $author);

			$author->restore();

			activity()->performedOn($author)
				->log('restored');
		} else {
			$this->authorize('delete', $author);

			$author->delete();

			activity()->performedOn($author)
				->log('deleted');
		}

		return redirect()
			->route('authors.show', $author);
	}

	/**
	 * Отображение псевдонимов авторов
	 *
	 * @param Author $author
	 * @return View
	 * @throws
	 */
	public function authors(Author $author)
	{
		if (!empty($author->group))
			$authors = $author->group->authors()->where('id', '!=', $author->id)->get();

		return view('author.authors', ['author' => $author, 'authors' => $authors ?? null]);
	}

	/**
	 * Группирование автора с другим
	 *
	 * @param Author $author
	 * @param Request $request
	 * @return Response
	 * @throws
	 */
	public function group(Request $request, Author $author)
	{
		$this->validate($request, ['author' => 'numeric|exists:authors,id']);

		$this->authorize('group', $author);

		$another_author = Author::accepted()->findOrFail($request->input('author'));

		$this->authorize('group', $another_author);

		if ($author->id == $another_author->id)
			return back()->withInput()
				->withErrors(__('author.group_error_1'));

		if (($author->group_id) and ($another_author->group_id) and ($author->group_id == $another_author->group_id))
			return back()->withInput()
				->withErrors(__('author.group_error_2', ['author_name' => $author->name, 'another_author_name' => $another_author->name]));

		if ($another_author->group_id)
			return back()->withInput()
				->withErrors(__('author.group_error_3', ['author_name' => $author->name]));

		if ((!$author->group_id) and (!$another_author->group_id)) {

			$group = new AuthorGroup;
			$group->last_name = $author->last_name;
			$group->first_name = $author->first_name;
			$group->save();

			$author->attach_to_group($group);
			$another_author->attach_to_group($group);

			activity()->performedOn($author)->log('group');
			activity()->performedOn($another_author)->log('group');

		} else {

			$group = AuthorGroup::findOrFail($author->group_id);
			$another_author->attach_to_group($group);

			activity()->performedOn($author)->log('group');
		}

		$group->count = Author::where('group_id', $group->id)->count();

		$group->save();

		return back()
			->with('success', __('author.group_success', ['author_name' => $author->name, 'another_author_name' => $another_author->name]));
	}

	/**
	 * Разгруппировка автора
	 *
	 * @param Author $author
	 * @return Response
	 * @throws
	 */
	public function ungroup(Author $author)
	{
		$this->authorize('ungroup', $author);

		$author->detach_from_group();

		activity()->performedOn($author)->log('ungroup');

		return back();
	}

	/**
	 * Получение форума автора
	 *
	 * @param Request $request
	 * @param Author $author
	 * @return View
	 * @throws
	 */
	public function forum(Request $request, Author $author)
	{
		$forum = $author->forum;

		if (empty($forum)) {
			$forum = new Forum;
			$forum->name = 'Форум автора: ' . $author->name;
			$forum->obj_id = $author->id;
			$forum->obj_type = 'author';
			$forum->save();

			$author->forum_id = $forum->id;
			$author->save();
		}

		$topics = $forum->topics()
			->with('last_post')
			->orderBy('last_post_id', 'desc')
			->simplePaginate();

		if (request()->ajax())
			return view('forum.show', compact('forum', 'topics'))->renderSections()['content'];
		else
			return view('forum.show', compact('forum', 'topics'));
	}

	/**
	 * Добавление или удаление автора в личную библиотеку
	 *
	 * @param Author $author
	 * @return array
	 * @throws
	 */

	public function toggle_my_library(Author $author)
	{
		$user_author_pivot = UserAuthor::where('author_id', $author->id)
			->where('user_id', auth()->id())
			->first();

		if (empty($user_author_pivot)) {
			UserAuthor::create(['author_id' => $author->id]);
			$author->refresh();

			return [
				'result' => 'attached',
				'added_to_favorites_count' => $author->added_to_favorites_count
			];
		} else {

			$user_author_pivot->delete();
			$author->refresh();

			return [
				'result' => 'detached',
				'added_to_favorites_count' => $author->added_to_favorites_count
			];
		}
	}

	/**
	 * Установка статуса прочтения автора
	 *
	 * @param Author $author
	 * @param string $status
	 * @return Response
	 * @throws
	 */

	public function read_status(Author $author, $status)
	{
		$status = AuthorStatus::updateOrCreate(
			['user_id' => auth()->id(), 'author_id' => $author->id],
			['status' => $status, 'user_updated_at' => now()]
		);

		return $status;
	}

	/**
	 * Поиск автора через js
	 *
	 * @param Request $request
	 * @return Paginator
	 * @throws
	 */

	public function search(Request $request)
	{
		$str = trim($request->input('q'));

		if (auth()->check()) {
			$managers = auth()->user()->managers()
				->with('manageable')
				->whereHasMorph(
					'manageable',
					['App\Author'],
					function (Builder $query) use ($str) {
						$query->where('name_helper', 'ILIKE', '%' . $str . '%');
					})
				->accepted()
				->get();
		}

		$query = Author::notMerged()
			->acceptedOrBelongsToAuthUser()
			->orderByRatingDesc();

		if (is_numeric($str)) {
			$query->where('id', pg_intval($str));
		} else {
			$query->fulltextSearch($str);

			if (!empty($managers) and $managers->isNotEmpty()) {
				$query->whereNotIn('id', $managers->pluck('manageable.id')->toArray());
			}
		}

		$authors = $query->simplePaginate();

		if (!empty($managers)) {
			foreach ($managers as $manager) {
				$authors->prepend($manager->manageable);
			}
		}

		return $authors;
	}

	/**
	 * Форма объединения авторов
	 *
	 * @param Request $request
	 * @return View
	 * @throws
	 */

	public function mergeForm(Request $request)
	{
		$this->authorize('merge', Author::class);

		$ids = $request->input('authors');

		if (is_string($ids))
			$ids = explode(',', $ids);

		$authors = Author::notMerged()->find($ids);

		if (empty($authors))
			return redirect()->route('authors');

		return view('author.merge', ['authors' => $authors]);
	}

	/**
	 * Объединение авторов
	 *
	 * @param Request $request
	 * @return array
	 * @throws
	 */

	public function merge(Request $request)
	{
		$this->authorize('merge', Author::class);

		$this->validate($request, [
			'main_author' => 'required',
			'authors' => 'required|array'
		]);

		$main_author = Author::any()->findOrFail($request->main_author);

		$authors = Author::any()->where('id', '!=', $main_author->id)
			->whereIn('id', $request->authors)
			->get();

		DB::transaction(function () use ($authors, $main_author) {

			foreach ($authors as $author) {
				// переносим книги
				/*
								$ids = $author->any_books()->any()->pluck('id')->toArray();

								if (count($ids) > 0) {
									$author->any_books()->any()->detach();
									$main_author->any_books()->any()->syncWithoutDetaching($ids);
								}
								*/
				$books = $author->any_books()->any()->withPivot('type')->get();

				if ($books->count() > 0) {
					foreach ($books as $book) {
						$sync[$book->id] = ['type' => $book->pivot->type];
					}

					$main_author->any_books()->syncWithoutDetaching($sync);
					$author->any_books()->any()->detach();
				}

				// переносим первую найденную биографию, если она не найдена у текущего автора
				if (empty($main_author->biography)) {

					if (!empty($author->biography)) {
						$biography = AuthorBiography::create([
							'text' => $author->biography->text,
							'author_id' => $main_author->id
						]);
						$main_author->biography_id = $biography->id;
						$main_author->save();
					}
				}

				$author->merged_at = Carbon::now();
				$author->redirect_to_author()->associate($main_author);
				$author->save();

				Artisan::call('refresh:author_counters', ['id' => $author->id]);

				activity()->performedOn($author)
					->log('merged');
			}

			$main_author->merged_at = null;
			$main_author->redirect_to_author_id = null;
			$main_author->save();

			Artisan::call('refresh:author_counters', ['id' => $main_author->id]);
		});

		return redirect()->route('authors.show', $main_author);
	}

	/**
	 * Получение файла со списком ссылок на книги автора
	 *
	 * @param Author $author
	 * @return array
	 * @throws
	 */

	public function allLinksToBooks(Author $author)
	{
		$books = $author->any_books()
			->onlyDownloadAccess()
			->with('files')
			->get();

		$urls = [];

		foreach ($books as $book) {
			foreach ($book->files as $file) {
				$urls[] = route('books.files.show', ['book' => $book, 'fileName' => $file->name]);
			}
		}

		$name = __('author.links_to_books') . $author->name . ' ' . now()->timestamp;

		return response(implode("\n", $urls), 200)
			->header('Content-Type', 'text/plain')
			->header('Content-Disposition', 'attachment; filename="' . $name . '.txt"');
	}

	/**
	 * Обновление счетчиков автора
	 *
	 * @param Author $author
	 * @return Response
	 * @throws
	 */

	public function refreshCounters(Author $author)
	{
		Artisan::call('refresh:author_counters', ['id' => $author->id]);

		return back();
	}

	public function activity_logs(Author $author)
	{
		$activityLogs = $author->activities()
			->latest()
			->simplePaginate();

		$activityLogs->load(['causer', 'subject' => function ($query) {
			$query->any();
		}]);

		return view('activity_log.index', compact('activityLogs'));
	}

	public function books($id)
	{
		return redirect()->route('authors.show', ['author' => $id]);
	}

	public function books_votes(Author $author)
	{
		$votes = BookVote::join("book_authors", "book_authors.book_id", "=", "book_votes.book_id")
			->where("author_id", $author->id)
			->orderBy('user_updated_at', 'desc')
			->with(["book", 'create_user.latest_user_achievements.achievement.image', 'create_user.groups'])
			->simplePaginate();

		$view = view('author.books_votes', ['votes' => $votes]);

		if (request()->ajax())
			return $view->renderSections()['content'];
		else
			return $view;
	}

	public function makeAccepted(Author $author)
	{
		$this->authorize('makeAccepted', $author);

		$author->statusAccepted();
		$author->save();

		activity()->performedOn($author)
			->log('make_accepted');

		return redirect()
			->route('authors.show', $author)
			->with('success', __('author.published'));
	}

	public function booksCloseAccess(Author $author)
	{
		$this->authorize('booksCloseAccess', $author);

		$books = $author->any_books()
			->readOrDownloadAccess()
			->get();

		foreach ($books as $book) {
			$book->readAccessDisable();
			$book->downloadAccessDisable();
			$book->save();

			activity()
				->performedOn($book)
				->withProperties([
					'read_access' => false,
					'download_access' => false,
					'secret_hide_reason' => '',
				])
				->log('change_access');
		}

		return redirect()
			->route('authors.show', $author)
			->with(['success' => __('author.books_access_closed')]);
	}

	public function comments(Author $author)
	{
		$books_ids = $author->any_books()
			->select('id')
			->pluck('id')
			->toArray();

		if (count($books_ids) < 1) $books_ids = [];

		$builder = Comment::query()
			->bookType()
			->whereIn('commentable_id', $books_ids);

		$resource = (new CommentSearchResource(request(), $builder))
			->setViewType('comment.list.default');

		$vars = $resource->getVars();

		$vars['author'] = $author;
		$vars['comments'] = $resource->getQuery()->simplePaginate();

		if (request()->ajax()) {
			if (request()->with_panel)
				return view('author.comments', $vars)
					->renderSections()['content'];
			else
				return view('comment.list', $vars);
		}

		return view('author.comments', $vars);
	}

	public function showParsedData()
	{
		$datas = AuthorParsedData::whereNotNull('email')
			->where('email', '!=', '')
			->get();

		return view('author.parsed_data', ['datas' => $datas]);
	}

	public function getAddressesForMailingToInviteSellingBooks()
	{
		$managers = Manager::authors()
			->with('user.notice_email', 'manageable.books')
			->where('can_sale', false)
			->whereHasMorph(
				'manageable', ['App\Author'],
				function (Builder $query) {
					$query->where('votes_count', '>', '20')
						->where('vote_average', '>', '7');
				}
			)
			->get();

		return view('author.get_addresses_for_mailing_to_invite_selling_books', ['managers' => $managers]);
	}

	public function photoShow(Author $author)
	{
		if (empty($author->photo) or $author->trashed())
			abort(404);

		return view('author.photo.show', ['author' => $author]);
	}
}
