<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\View;

class NotificationController extends Controller
{
	/**
	 * Список уведомлений
	 *
	 * @param User $user
	 * @return \Illuminate\View\View
	 * @return View
	 * @throws
	 */

	public function index(User $user)
	{
		$this->authorize('view_notification', $user);

		$notifications = $user->notifications()
			->simplePaginate(15);

		if ($user->getUnreadNotificationsCount() > 0) {
			if ($user->id == auth()->id()) {
				$user->unreadNotifications()->update(['read_at' => now()]);
				$user->flushCachedUnreadNotificationsCount();
			}
		}

		return view('notifications.index', ['notifications' => $notifications]);
	}
}
