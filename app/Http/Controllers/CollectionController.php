<?php

namespace App\Http\Controllers;

use App\Book;
use App\CollectedBook;
use App\Collection;
use App\CollectionUser;
use App\Enums\AuthorEnum;
use App\Enums\UserSubscriptionsEventNotificationType;
use App\Http\Requests\StoreCollectedBook;
use App\Http\Requests\StoreCollection;
use App\Http\Requests\StoreCollectionUser;
use App\Http\Requests\UpdateCollectionUser;
use App\Http\SearchResource\CollectionSearchResource;
use App\Library\BookSearchResource;
use App\User;
use App\UserFavoriteCollection;
use App\UserSubscriptionsEventNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CollectionController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$builder = Collection::acceptedOrBelongsToAuthUser();

		$resource = (new CollectionSearchResource(request(), $builder));
		$vars = $resource->getVars();

		$vars['collections'] = $resource->getQuery()
			->with('latest_books.writers')
			->simplePaginate();

		if (auth()->check()) {
			$vars['collections']->load(['collectionUser' => function ($query) {
				$query->where('user_id', auth()->id());
			}]);
		}

		$vars['collections']->load('authUserLike');

		if (request()->ajax())
			return view('collection.list', $vars);

		return view('collection.index', $vars);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 * @throws
	 */
	public function create()
	{
		$this->authorize('create', Collection::class);

		return view('collection.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreCollection $request
	 * @return Response
	 * @throws
	 */
	public function store(StoreCollection $request)
	{
		$this->authorize('create', Collection::class);

		$collection = new Collection($request->all());
		$collection->save();

		return redirect()
			->route('users.collections.created', ['user' => $collection->create_user])
			->with(['success' => __('collection.successfully_created')]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Collection $collection
	 * @return Response
	 * @throws
	 */
	public function show(Collection $collection)
	{
		$this->authorize('view', $collection);

		$collection->viewsIncrement();

		return view('collection.show', compact('collection'));
	}

	/**
	 * Книги в подборке
	 *
	 * @param Collection $collection
	 * @return Response
	 * @throws
	 */
	public function books(Collection $collection)
	{
		if (auth()->check()) {
			$collection->load(['collectionUser' => function ($query) {
				$query->where('user_id', auth()->id());
			}]);
		}

		$this->authorize('view', $collection);

		$builder = $collection->books();

		$resource = (new BookSearchResource(request(), $builder))
			->defaultSorting('collection_number_asc')
			->setSimplePaginate(true)
			->addOrder('collection_number_asc', function ($query) {
				$query->orderBy('collected_books.number', 'asc')
					->orderBy('id', 'desc');
			})
			->addOrder('collection_number_desc', function ($query) {
				$query->orderBy('collected_books.number', 'desc')
					->orderBy('id', 'asc');
			})
			->addOrder('oldest_added_to_collection', function ($query) {
				$query->orderBy('collected_books.created_at', 'asc');
			})
			->addOrder('latest_added_to_collection', function ($query) {
				$query->orderBy('collected_books.created_at', 'desc');
			})
			->setViewType('collection.book.book');

		$vars = $resource->getVars();

		$vars['collection'] = $collection;
		$vars['books'] = $resource->getQuery()->simplePaginate();

		$vars['books']->loadMissing(['collected_book.create_user']);

		if (request()->ajax())
			return $resource->renderAjax($vars);

		return view('collection.books', $vars);
	}

	/**
	 * Сотрудники подборки
	 *
	 * @param Collection $collection
	 * @return Response
	 * @throws
	 */
	public function users(Collection $collection)
	{
		if (auth()->check()) {
			$collection->load(['collectionUser' => function ($query) {
				$query->where('user_id', auth()->id());
			}]);
		}

		$this->authorize('view', $collection);

		$collectionUsers = $collection->collectionUser()
			->get();

		$collectionUsers->loadMissing(['user.latest_user_achievements', 'user.groups']);

		return view('collection.user.index', [
			'collection' => $collection,
			'collectionUsers' => $collectionUsers
		]);
	}

	/**
	 * Комментарии подборки
	 *
	 * @param Collection $collection
	 * @return Response
	 * @throws
	 */
	public function comments(Collection $collection)
	{
		$this->authorize('view', $collection);

		$comments = $collection->comments()
			->roots()
			->latest()
			->with([
				'create_user.avatar',
				'create_user.groups',
				'commentable',
				'create_user.latest_user_achievements.achievement.image',
				'votes' => function ($query) {
					$query->where("create_user_id", auth()->id());
				}
			])
			->paginate(config('litlife.comments_on_page_count'));

		if (auth()->check()) {
			$subscription = $collection->eventNotificationSubscriptions()
				->where('notifiable_user_id', auth()->id())
				->first();
		}

		return view('collection.comments', [
			'comments' => $comments,
			'collection' => $collection,
			'subscription' => $subscription ?? null,
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Collection $collection
	 * @return Response
	 * @throws
	 */
	public function edit(Collection $collection)
	{
		$this->authorize('update', $collection);

		return view('collection.edit', compact('collection'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreCollection $request
	 * @param Collection $collection
	 * @return Response
	 * @throws
	 */
	public function update(StoreCollection $request, Collection $collection)
	{
		$this->authorize('update', $collection);

		if ($request->who_can_add == 'me' and $collection->who_can_add != 'me') {
			$array = $collection->users()
				->select('users.id')
				->get()
				->pluck('id')->toArray();

			$array[] = $collection->create_user_id;

			if ($collection->books()->whereNotIn('collected_books.create_user_id', $array)->first()) {
				return redirect()
					->route('collections.edit', $collection)
					->withErrors(['who_can_add' =>
						__('collection.you_cant_select_only_me_because_there_are_books_added_by_other_users_in_the_collection', ['value' => __('collection.who_can_add_array.me')])])
					->withInput();
			}
		}

		$collection->fill($request->all());
		$collection->save();

		return redirect()
			->route('collections.edit', $collection)
			->with(['success' => __('collection.data_successfully_updated')]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  $id integer
	 * @return Response
	 * @throws
	 */
	public function destroy($id)
	{
		$item = Collection::any()
			->findOrFail($id);

		if ($item->trashed()) {
			$this->authorize('restore', $item);

			$item->restore();
		} else {
			$this->authorize('delete', $item);

			$item->delete();
		}

		return $item;
	}

	/**
	 *
	 *
	 * @param Collection $collection
	 * @return array
	 * @throws
	 */
	public function toggleToFavorites(Collection $collection)
	{
		$user = auth()->user();

		$pivot = $collection->usersAddedToFavoritesPivot()
			->where('user_id', $user->id)
			->first();

		if (empty($pivot)) {
			$pivot = new UserFavoriteCollection(['user_id' => $user->id]);
			$collection->usersAddedToFavoritesPivot()->save($pivot);
		} else {
			$pivot->delete();
			unset($pivot);
		}

		$collection->refresh();

		if (!empty($pivot))
			return ['result' => 'attached', 'count' => $collection->added_to_favorites_users_count];
		else
			return ['result' => 'detached', 'count' => $collection->added_to_favorites_users_count];
	}

	/**
	 * Выбор книг для добавления в подборку
	 *
	 * @param Collection $collection
	 * @return Response
	 * @throws
	 */
	public function booksSelect(Collection $collection)
	{
		$this->authorize('addBook', $collection);

		if (old('book_id'))
			$book = Book::findOrFail(old('book_id'));

		$max = intval($collection->collected()
			->max('number'));

		return view('collection.book.attach', [
			'collection' => $collection,
			'book' => $book ?? null,
			'max' => $max + 1
		]);
	}

	/**
	 * Добавление книги в подборку
	 *
	 * @param StoreCollectedBook $request
	 * @param Collection $collection
	 * @return Response
	 * @throws
	 */
	public function booksAttach(StoreCollectedBook $request, Collection $collection)
	{
		$this->authorize('addBook', $collection);

		$book = Book::findOrFail($request->book_id);

		CollectedBook::updateOrCreate(
			[
				'collection_id' => $collection->id,
				'book_id' => $book->id
			],
			[
				'number' => $request->number,
				'comment' => $request->comment
			]
		);

		$collection->latest_updates_at = now();
		$collection->save();

		return redirect()
			->route('collections.books', $collection)
			->with(['success' => __('The book was successfully added to the collection')]);
	}

	/**
	 * Удаление книги из подборки
	 *
	 * @param Collection $collection
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function booksDetach(Collection $collection, Book $book)
	{
		$this->authorize('detachBook', $collection);

		$collected_books = CollectedBook::where('collection_id', $collection->id)
			->where('book_id', $book->id)
			->get();

		foreach ($collected_books as $collected_book)
			$collected_book->delete();

		$collection->latest_updates_at = now();
		$collection->save();

		return redirect()
			->route('collections.books', $collection)
			->with(['success' => __('The book was successfully removed from the collection')]);
	}

	/**
	 * Список книг для добавления в подборку
	 *
	 * @param Request $request
	 * @param Collection $collection
	 * @return Response
	 * @throws
	 */
	public function searchList(Request $request, Collection $collection)
	{
		if (!$request->ajax())
			return redirect()->route('collections.books.select', ['collection' => $collection]);

		$str = trim($request->input('query'));

		if (is_numeric($str)) {
			$query = Book::where('id', $str)
				->simplePaginate(10);
		} else {

			$query = Book::where(function ($query) use ($str) {
				return $query->titleAuthorsFulltextSearch($str)
					->when((mb_strlen($str) > 10), function ($query) use ($str) {
						return $query->orWhere('pi_isbn', 'ilike', '%' . $str . '%');
					});
			})->orderByRaw('"main_book_id" nulls first')
				->orderByRatingDesc();
		}

		$books = $query->acceptedOrBelongsToAuthUser()
			->with([
				'authors.managers',
				'sequences',
				'cover',
				'collections' => function ($query) use ($collection) {
					$query->where('collections.id', $collection->id);
				}])
			->simplePaginate(10);

		foreach ($books as $book) {
			$book->setRelation('writers', $book->getAuthorsWithType(AuthorEnum::Writer));
			$book->setRelation('editors', $book->getAuthorsWithType(AuthorEnum::Editor));
			$book->setRelation('illustrators', $book->getAuthorsWithType(AuthorEnum::Illustrator));
			$book->setRelation('translators', $book->getAuthorsWithType(AuthorEnum::Translator));
			$book->setRelation('compilers', $book->getAuthorsWithType(AuthorEnum::Compiler));
		}

		return view('collection.book.list', compact('books'));
	}

	/**
	 *
	 *
	 * @param Book $book
	 * @param Collection $collection
	 * @return Response
	 * @throws
	 */
	public function booksSelectedItem(Book $book)
	{
		return view('collection.book.selected_item', compact('book'));
	}

	/**
	 * Редактирование данных книги в подборке
	 *
	 * @param Collection $collection
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function collectedBookEdit(Collection $collection, Book $book)
	{
		$this->authorize('detachBook', $collection);

		$collected_book = CollectedBook::where('collection_id', $collection->id)
			->where('book_id', $book->id)
			->first();

		return view('collection.book.edit', compact('collection', 'book', 'collected_book'));
	}

	/**
	 * Редактирование данных книги в подборке
	 *
	 * @param StoreCollectedBook $request
	 * @param Collection $collection
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function collectedBookUpdate(StoreCollectedBook $request, Collection $collection, Book $book)
	{
		$this->authorize('detachBook', $collection);

		$collected_book = CollectedBook::where('collection_id', $collection->id)
			->where('book_id', $book->id)
			->first();

		$collected_book->fill($request->all());
		$collected_book->save();

		$collection->latest_updates_at = now();
		$collection->save();

		return redirect()
			->route('collections.books.edit', ['collection' => $collection, 'book' => $book])
			->with(['success' => __('The book data in the collection was saved successfully')]);
	}

	/**
	 * Подписаться или отписаться на присылание уведомления при появление нового комментария в подборке
	 *
	 * @param Collection $collection
	 * @return array
	 * @throws
	 */
	public function eventNotificationSubcriptionsToggle(Collection $collection)
	{
		$this->authorize('subscribeToEventNotifications', $collection);

		$subscription = $collection->eventNotificationSubscriptions()
			->where('notifiable_user_id', auth()->id())
			->first();

		if (empty($subscription)) {
			$subscription = new UserSubscriptionsEventNotification();
			$subscription->notifiable_user_id = auth()->id();
			$subscription->event_type = UserSubscriptionsEventNotificationType::NewComment;
			$collection->eventNotificationSubscriptions()->save($subscription);

			if (\request()->ajax()) {
				return [
					'status' => 'subscribed',
					'subscription' => $subscription
				];
			} else {
				return redirect()
					->route('collections.comments', $collection)
					->with(['success' => __('collection.notifications_for_new_collection_comments_has_been_successfully_enabled', ['collection_title' => $collection->title])]);
			}

		} else {
			$subscription->delete();

			if (\request()->ajax()) {
				return [
					'status' => 'unsubscribed'
				];
			} else {
				return redirect()
					->route('collections.comments', $collection)
					->with(['success' => __('collection.notifications_about_new_comments_to_the_collection_successfully_disabled', ['collection_title' => $collection->title])]);
			}
		}
	}

	public function carouselLatestBooks(Collection $collection)
	{
		$books = $collection->latest_books()->simplePaginate();

		return view('collection.carousel_latest_books', compact('books'));
	}

	/**
	 * Форма добавления пользователя в подборку
	 *
	 * @param Collection $collection
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function createUser(Collection $collection, User $user)
	{
		$this->authorize('createUser', $collection);

		$collectionUser = new CollectionUser();

		return view('collection.user.create', compact('collection', 'collectionUser'));
	}

	/**
	 * Добавление пользователя в подборку
	 *
	 * @param StoreCollectionUser $request
	 * @param Collection $collection
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function storeUser(StoreCollectionUser $request, Collection $collection)
	{
		$this->authorize('createUser', $collection);

		$validated = $request->validated();

		if ($validated['user_id'] == $collection->create_user->id)
			return redirect()
				->route('collections.users.create', $collection)
				->withErrors(['user_id' => __('collection.the_user_has_already_been_added')]);

		$collectionUser = $collection->collectionUser()
			->withTrashed()
			->where('user_id', $validated['user_id'])
			->first();

		if ($collectionUser) {
			if ($collectionUser->trashed()) {
				$collectionUser->restore();
			} else {
				return redirect()
					->route('collections.users.create', $collection)
					->withErrors(['user_id' => __('collection.the_user_has_already_been_added')]);
			}
		} else {
			$collectionUser = new CollectionUser;
		}

		$collectionUser->fill($validated);
		$collectionUser->create_user()->associate(auth()->user());
		$collection->collectionUser()->save($collectionUser);

		$collection->refreshUsersCount();
		$collection->save();

		return redirect()
			->route('collections.users.index', ['collection' => $collection])
			->with(['success' => __('collection_user.user_successfully_added')]);
	}

	/**
	 * Форма редактирования пользователя в подборке
	 *
	 * @param Collection $collection
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function editUser(Collection $collection, User $user)
	{
		$this->authorize('editUser', $collection);

		$collectionUser = $collection->collectionUser()
			->where('user_id', $user->id)
			->firstOrFail();

		return view('collection.user.edit', compact('collection', 'collectionUser', 'user'));
	}

	/**
	 * Обновления данных пользователя в подборке
	 *
	 * @param UpdateCollectionUser $request
	 * @param Collection $collection
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function updateUser(UpdateCollectionUser $request, Collection $collection, User $user)
	{
		$this->authorize('editUser', $collection);

		$collectionUser = $collection->collectionUser()->where('user_id', $user->id)->firstOrFail();
		$collectionUser->fill($request->validated());
		$collectionUser->save();

		return redirect()
			->route('collections.users.index', ['collection' => $collection])
			->with(['success' => __('collection_user.user_data_is_saved')]);
	}

	/**
	 * Удаления пользователя из подборки
	 *
	 * @param Request $request
	 * @param Collection $collection
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function deleteUser(Request $request, Collection $collection, User $user)
	{
		$this->authorize('deleteUser', $collection);

		$collectionUser = $collection->collectionUser()
			->withTrashed()
			->where('user_id', $user->id)
			->firstOrFail();

		if ($collectionUser->trashed())
			$collectionUser->restore();
		else
			$collectionUser->delete();

		$collection->refreshUsersCount();
		$collection->save();

		if ($request->ajax())
			return $collectionUser;
		else
			return redirect()->route('collections.users.index', $collection);
	}
}
