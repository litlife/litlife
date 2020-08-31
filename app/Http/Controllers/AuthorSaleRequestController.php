<?php

namespace App\Http\Controllers;

use App\AuthorSaleRequest;
use App\Enums\StatusEnum;
use App\Notifications\AuthorSaleRequestAcceptedNotification;
use App\Notifications\AuthorSaleRequestRejectedNotification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthorSaleRequestController extends Controller
{
	/**
	 * Список заявок
	 *
	 * @return Response
	 * @throws
	 */
	public function index()
	{
		$this->authorize('author_sale_request_review', User::class);

		$requests = AuthorSaleRequest::orderByField('status', [StatusEnum::getValue('OnReview')])
			->orderStatusChangedDesc()
			->with(['manager' => function ($query) {
				$query->withTrashed();
			}])
			->simplePaginate();

		return view('author.sales.request.index', compact('requests'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param AuthorSaleRequest $request
	 * @return Response
	 * @throws
	 */
	public function show(AuthorSaleRequest $request)
	{
		$this->authorize('show', $request);

		return view('author.sales.request.show', ['item' => $request]);
	}

	/**
	 * Подтвердить заявку
	 *
	 * @param AuthorSaleRequest $request
	 * @return Response
	 * @throws
	 */
	public function accept(AuthorSaleRequest $request)
	{
		$this->authorize('accept', $request);

		$request->statusAccepted();
		$request->manager->can_sale = true;
		$request->push();

		$request->author->convertAllBooksInTheOldFormatToTheNewOne();

		AuthorSaleRequest::flushCachedOnModerationCount();

		$request->create_user->notify(new AuthorSaleRequestAcceptedNotification($request));

		return redirect()
			->route('authors.sales_requests.show', ['request' => $request->id])
			->with(['success' => __('author_sale_request.you_accept_review')]);
	}

	/**
	 * Отклонить заявку
	 *
	 * @param Request $r
	 * @param AuthorSaleRequest $request
	 * @return Response
	 * @throws
	 */
	public function reject(Request $r, AuthorSaleRequest $request)
	{
		$this->authorize('reject', $request);

		$this->validate($r, ['review_comment' => 'required']);

		$request->review_comment = trim($r->review_comment);
		$request->statusReject();
		$request->push();

		AuthorSaleRequest::flushCachedOnModerationCount();

		$request->create_user->notify(new AuthorSaleRequestRejectedNotification($request));

		return redirect()
			->route('authors.sales_requests.show', ['request' => $request->id])
			->with(['success' => __('author_sale_request.you_reject_review')]);
	}

	/**
	 * Начать рассматривать заявку
	 *
	 * @param AuthorSaleRequest $request
	 * @return Response
	 * @throws
	 */
	public function startReview(AuthorSaleRequest $request)
	{
		$this->authorize('start_review', $request);

		$request->statusReviewStarts();
		$request->push();

		return redirect()
			->route('authors.sales_requests.show', ['request' => $request->id]);
	}

	/**
	 * Прекратить рассматривать заявку
	 *
	 * @param AuthorSaleRequest $request
	 * @return Response
	 * @throws
	 */
	public function stopReview(AuthorSaleRequest $request)
	{
		$this->authorize('stop_review', $request);

		$request->statusSentForReview();
		$request->push();

		return redirect()
			->route('authors.sales_requests.index')
			->with(['success' => __('author_sale_request.you_stop_review')]);
	}

	/**
	 * Интерактивное руководство для авторов, которые хотят начать продавать книги
	 *
	 * @return Response
	 */
	public function howToStartSellingBooks()
	{
		if (auth()->check()) {
			$user = auth()->user();
			$wallets = $user->wallets;
		}

		if (!empty($user)) {
			$manager = $user->managers()
				->authors()
				->accepted()
				->first();

			if (empty($manager)) {
				$manager = $user->managers()
					->authors()
					->first();
			} elseif ($manager->isAccepted()) {

				$books = $manager->manageable
					->written_books()
					->get();

				$salesRequest = $manager->manageable
					->sales_request()
					->latest()
					->first();

				if ($manager->can_sale and $books) {
					$booksOnSale = $manager->manageable
						->written_books()
						->where('price', '>', 0)
						->get();
				}
			}
		}

		$fileExtensionsWhichCanExtractText = array_diff(config('litlife.book_allowed_file_extensions'), config('litlife.no_need_convert'));

		return view('how_to_start_selling_books', get_defined_vars());
	}
}
