<?php

namespace App\Http\Controllers;

use App\Author;
use App\AuthorSaleRequest;
use App\Manager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AuthorManagerController extends Controller
{
	/**
	 * Список редакторов автора
	 *
	 * @param Author $author
	 * @param
	 * @return View
	 * @throws
	 */
	public function index(Author $author)
	{
		$this->authorize('viewManagers', $author);

		$managers = $author->managers()
			->accepted()
			->get();

		return view('author.manager.index', compact('author', 'managers'));
	}

	/**
	 * Сохранение нового редактора автора
	 *
	 * @param Request $request
	 * @param Author $author
	 * @return Response
	 * @throws
	 */
	public function store(Request $request, Author $author)
	{
		$this->authorize('create', Manager::class);

		$this->validate($request, [
			'user_id' => 'required|exists:users,id',
			'character' => 'required|in:' . implode(',', config('litlife.manager_characters'))
		], [], __('manager'));

		$count = $author->managers()
			->where('character', 'author')
			->count();

		if ($count > 0) {
			return back()
				->withErrors(['user_id' => __('The author has already been verified. Delete the other verification to add a new one')]);
		}

		$manager = new Manager;
		$manager->user_id = $request->input('user_id');
		$manager->character = $request->input('character');
		$manager->statusAccepted();

		$author->managers()->save($manager);

		Manager::flushCachedOnModerationCount();

		$manager->user->attachUserGroupByNameIfExists('Автор');

		return back();
	}

	/**
	 * Просмотр заявки
	 *
	 * @param Manager $manager
	 * @return View
	 * @throws
	 */
	public function show(Manager $manager)
	{
		$this->authorize('view', $manager);

		return view('author.manager.show', compact('manager'));
	}

	/**
	 * Форма запроса на верификацию "Я автор"
	 *
	 * @param Request $request
	 * @param Author $author
	 * @return View
	 * @throws
	 */
	public function verificationRequest(Request $request, Author $author)
	{
		$this->authorize('verficationRequest', $author);

		$manager = $author->managers()
			->where('user_id', auth()->id())
			->first();

		return view('author.manager.verification', compact('author', 'manager'));
	}

	/**
	 * Сохранение запроса Я автор
	 *
	 * @param Request $request
	 * @param Author $author
	 * @return Response
	 * @throws
	 */
	public function verificationRequestSave(Request $request, Author $author)
	{
		$this->authorize('verficationRequest', $author);

		$this->validate($request, [
			'comment' => 'required'
		], [], __('manager'));

		$manager = new Manager;
		$manager->character = 'author';
		$manager->user_id = auth()->id();
		$manager->comment = $request->input('comment');

		if ($author->isPrivate())
			$manager->statusPrivate();
		else
			$manager->statusSentForReview();

		$author->managers()->save($manager);

		Manager::flushCachedOnModerationCount();

		if ($manager->isSentForReview())
			return redirect()
				->route('authors.show', $author)
				->with(['success' => __('manager.request_has_been_sent')]);
		else
			return redirect()
				->route('authors.show', $author)
				->with(['success' => __('manager.request_is_saved_and_will_be_sent_for_review_after_the_authors_publication')]);
	}

	/**
	 * Форма запроса запроса на редактора
	 *
	 * @param Request $request
	 * @param Author $author
	 * @return View
	 * @throws
	 */
	public function editorRequest(Request $request, Author $author)
	{
		$this->authorize('editorRequest', $author);

		$manager = $author->managers()
			->where('user_id', auth()->id())
			->first();

		return view('author.manager.editor_request', compact('author', 'manager'));
	}

	/**
	 * Сохранение запроса стать редактором
	 *
	 * @param Request $request
	 * @param Author $author
	 * @return Response
	 * @throws
	 */
	public function editorRequestSave(Request $request, Author $author)
	{
		$this->authorize('editorRequest', $author);

		$this->validate($request, [
			'comment' => 'required'
		], [], __('manager'));

		$manager = new Manager;
		$manager->character = 'editor';
		$manager->user_id = auth()->id();
		$manager->comment = $request->input('comment');

		if ($author->isPrivate())
			$manager->statusPrivate();
		else
			$manager->statusSentForReview();

		$author->managers()->save($manager);

		Manager::flushCachedOnModerationCount();

		if ($manager->isSentForReview())
			return redirect()
				->route('authors.show', $author)
				->with(['success' => __('manager.request_has_been_sent')]);
		else
			return redirect()
				->route('authors.show', $author)
				->with(['success' => __('manager.request_is_saved_and_will_be_sent_for_review_after_the_authors_publication')]);
	}

	/**
	 * Отображение формы заявки на получения разрешения продажи книг
	 *
	 * @param Author $author
	 * @return View
	 * @throws
	 */
	public function salesRequestForm(Author $author)
	{
		$salesRequest = $author->sales_request()
			->where('create_user_id', auth()->id())
			->whereStatusIn(['OnReview', 'ReviewStarts'])
			->first();

		if (!empty($salesRequest))
			return redirect()->route('authors.sales_requests.show', $salesRequest);

		$this->authorize('sales_request', $author);

		if ($author->written_books()->acceptedAndSentForReview()->sum('characters_count') < config('litlife.the_total_number_of_characters_of_the_authors_books_in_order_to_be_allowed_to_send_a_request_for_permission_to_sell_books'))
			$isEnoughBooksTextCharacters = false;
		else
			$isEnoughBooksTextCharacters = true;

		if ($author->written_books()->acceptedAndSentForReview()->whereCreator(auth()->user())->first())
			$authorHasBooksAddedByAuthUser = true;
		else
			$authorHasBooksAddedByAuthUser = false;

		$completeBooksCount = $author->written_books()->acceptedAndSentForReview()->whereReadyStatus('complete')->count();

		return view('author.sales.request', compact('author',
			'salesRequest',
			'isEnoughBooksTextCharacters',
			'authorHasBooksAddedByAuthUser',
			'completeBooksCount'
		));
	}

	/**
	 * Сохранение заявки на получения разрешения продажи книг
	 *
	 * @param Author $author
	 * @return View
	 * @throws
	 */
	public function salesRequestStore(Request $request, Author $author)
	{
		$this->authorize('sales_request', $author);

		$completeBooksCount = $author->written_books()->acceptedAndSentForReview()->whereReadyStatus('complete')->count();

		if ($completeBooksCount < 1)
			return back()
				->withErrors([__('author_sale_request.to_send_a_request_the_author_must_have_at_least_one_finished_book')])
				->withInput($request->all());

		if (!$author->written_books()->acceptedAndSentForReview()->whereCreator(auth()->user())->first())
			return back()
				->withErrors([__('author_sale_request.your_author_page_must_have_at_least_one_book_added_by_you')])
				->withInput($request->all());

		if ($author->written_books()->acceptedAndSentForReview()->sum('characters_count') < config('litlife.the_total_number_of_characters_of_the_authors_books_in_order_to_be_allowed_to_send_a_request_for_permission_to_sell_books'))
			return back()
				->withErrors([__('author_sale_request.please_add_another_book_to_reach_the_required_number_of_characters')])
				->withInput($request->all());

		$this->validate($request, [
			'text' => 'required|min:10',
			'rules_accepted' => 'accepted'
		], [], __('author_sale_request'));

		$salesRequest = $author->sales_request()
			->where('create_user_id', auth()->id())
			->sentOnReview()
			->latest()
			->first();

		if (empty($salesRequest)) {

			$manager = $author->managers()
				->where('character', 'author')
				->where('user_id', auth()->id())
				->firstOrFail();

			$salesRequest = new AuthorSaleRequest;
			$salesRequest->manager_id = $manager->id;
		}
		$salesRequest->autoAssociateAuthUser();
		$salesRequest->author_id = $author->id;
		$salesRequest->text = $request->text;
		$salesRequest->statusSentForReview();
		$salesRequest->save();

		AuthorSaleRequest::flushCachedOnModerationCount();

		return redirect()
			->route('authors.sales_requests.show', ['request' => $salesRequest]);
	}

	/**
	 * Отключение возможности продавать книги для автора
	 *
	 * @param Author $author
	 * @return View
	 * @throws
	 */
	public function salesDisable(Author $author)
	{
		$this->authorize('salesDisable', $author);

		foreach ($author->managers as $manager) {
			if ($manager->isAccepted() and $manager->can_sale)
				$manager->can_sale = false;

			$manager->save();
		}

		foreach ($author->books as $book) {
			if ($book->isForSale()) {
				$book->changePrice(0);
				$book->downloadAccessDisable();
				$book->readAccessDisable();
				$book->save();
			}
		}

		return redirect()
			->route('authors.show', $author)
			->with(['success' => __('manager.ability_to_sell_books_for_the_author_is_disabled')]);
	}
}
