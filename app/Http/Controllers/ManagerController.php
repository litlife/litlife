<?php

namespace App\Http\Controllers;

use App\Author;
use App\AuthorSaleRequest;
use App\Enums\StatusEnum;
use App\Manager;
use App\Notifications\AuthorManagerAcceptedNotification;
use App\Notifications\AuthorManagerRejectedNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ManagerController extends Controller
{
	/**
	 * Список редакторов на проверке
	 *
	 * @return View
	 * @throws
	 */
	public function on_check()
	{
		$this->authorize('viewOnCheck', Manager::class);

		$managers = Manager::with([
			'user' => function ($query) {
				$query->any();
			},
			'user.avatar',
			'manageable' => function ($query) {
				$query->any();
			},
			'status_changed_user'
		])
			->whereStatusNot('Private')
			->orderByField('status', [StatusEnum::getValue('OnReview'), StatusEnum::getValue('ReviewStarts')])
			->orderStatusChangedDesc()
			->whereHasMorph('manageable', ['App\Author'], function (Builder $query) {
				$query->whereStatusNot('Private')
					->whereStatusNot('OnReview')
					->withTrashed();
			})
			->paginate();

		return view('manager.on_check', compact('managers'));
	}

	/**
	 * Удаление редактора
	 *
	 * @param Manager $manager
	 * @return Response
	 * @throws
	 */
	public function destroy(Manager $manager)
	{
		$this->authorize('delete', $manager);

		DB::transaction(function () use ($manager) {

			$saleRequests = $manager->saleRequests()
				->whereStatusIn(['OnReview', 'ReviewStarts'])
				->get();

			foreach ($saleRequests as $saleRequest)
				$saleRequest->delete();

			AuthorSaleRequest::flushCachedOnModerationCount();

			$manager->delete();

			if (!empty($manager->user)) {
				if ($manager->manageable instanceof Author and $manager->character == 'author') {
					$manager->user->detachUserGroupByNameIfExists('Автор');
				}
			}

		});

		Manager::flushCachedOnModerationCount();

		if (request()->ajax())
			return $manager;
		else
			return back();
	}

	/**
	 * Одобрение заявки
	 *
	 * @param Manager $manager
	 * @return mixed
	 * @throws
	 */
	public function approve(Manager $manager)
	{
		$this->authorize('approve', $manager);

		if (!$manager->manageable->isAccepted())
			return redirect()
				->route('managers.on_check')
				->with(['success' => __('manager.the_author_is_not_published')]);

		$manager->statusAccepted();
		$manager->save();

		Manager::flushCachedOnModerationCount();

		if (!empty($manager->user)) {
			if ($manager->manageable instanceof Author and $manager->character == 'author') {
				$manager->user->attachUserGroupByNameIfExists('Автор');
				$manager->user->notify(new AuthorManagerAcceptedNotification($manager));
			}
		}

		if (request()->ajax())
			return view('manager.alert', ['item' => $manager]);
		else
			return redirect()
				->route('managers.on_check')
				->with(['success' => __('manager.request_approved')]);
	}

	/**
	 * Отклонение заявки
	 *
	 * @param Manager $manager
	 * @return mixed
	 * @throws
	 */
	public function decline(Manager $manager)
	{
		$this->authorize('decline', $manager);

		$manager->statusReject();
		$manager->save();

		Manager::flushCachedOnModerationCount();

		if (!empty($manager->user)) {
			if ($manager->manageable instanceof Author and $manager->character == 'author')
				$manager->user->notify(new AuthorManagerRejectedNotification($manager));
		}

		if (request()->ajax())
			return view('manager.alert', ['item' => $manager]);
		else
			return redirect()
				->route('managers.on_check')
				->with(['success' => __('manager.declined')]);
	}

	/**
	 * Начать рассматривать заявку
	 *
	 * @param Manager $manager
	 * @return mixed
	 * @throws
	 */
	public function startReview(Manager $manager)
	{
		$this->authorize('startReview', $manager);

		$manager->statusReviewStarts();
		$manager->save();

		Manager::flushCachedOnModerationCount();

		if (request()->ajax())
			return view('manager.alert', ['item' => $manager]);
		else
			return redirect()
				->route('managers.on_check')
				->with(['success' => __('manager.declined')]);
	}

	/**
	 * Начать рассматривать заявку
	 *
	 * @param Manager $manager
	 * @return mixed
	 * @throws
	 */
	public function stopReview(Manager $manager)
	{
		$this->authorize('stopReview', $manager);

		$manager->statusSentForReview();
		$manager->save();

		Manager::flushCachedOnModerationCount();

		if (request()->ajax())
			return view('manager.alert', ['item' => $manager]);
		else
			return redirect()
				->route('managers.on_check')
				->with(['success' => __('manager.declined')]);
	}
}
