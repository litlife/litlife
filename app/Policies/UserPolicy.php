<?php

namespace App\Policies;

use App\Enums\TransactionType;
use App\Enums\UserAccountPermissionValues;
use App\Enums\UserGroupEnum;
use App\User;

class UserPolicy extends Policy
{
	/**
	 * Может ли пользователь редактировать профиль пользователя
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return bool
	 */
	public function update(User $auth_user, User $user)
	{
		if ($auth_user->id == $user->id) {
			if ($auth_user->getPermission('edit_profile'))
				// это профиль пользователя и он может его редактировать
				return true;
		} else {
			if ($auth_user->getPermission('edit_other_profile'))
				// это чужой профиль пользователя и его можно редактировать пользователю
				return true;
		}

		return false;
	}

	/**
	 * Может ли пользователь комментировать записи на стене пользователя
	 *
	 * перенесено в BlogPolicy reply
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return bool
	 */
	/*
		public function replyOnWall(User $auth_user, User $user)
		{
			if (!$auth_user->getPermission('Blog'))
				return false;

			if ($auth_user->id == $user->id)
				// можно комментировать на своей стене
				return true;

			switch ($user->account_permissions->comment_on_the_wall) {
				case 'everyone':
					return true;
					break;
				case 'me':
					if ($auth_user->id == $user->id)
						return true;
					break;
				case 'friends':
					$relation = $auth_user->relationship->where('user_id2', $user->id)->first();

					if ((isset($relation->status)) and ($relation->status == 'Friend'))
						return true;
					break;
				case 'friends_and_subscribers':
					$relation = $auth_user->relationship->where('user_id2', $user->id)->first();

					if ((isset($relation->status)) and (in_array($relation->status, ['Friend', 'Subscriber'])))
						return true;
					break;
			}

			return false;
		}
		*/

	/**
	 * Может ли пользователь писать на стене пользователя
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return bool
	 */

	public function writeOnWall(User $auth_user, User $user)
	{
		if (!$auth_user->getPermission('Blog'))
			return false;

		if ($auth_user->id == $user->id)
			// можно писать на своей стене
			return true;

		if (!empty($auth_user->getPermission('access_send_private_messages_avoid_privacy_and_blacklists')))
			return true;

		// если кто добавил кого то в черный список, то обмен сообщениями запрещен
		if ($auth_user->hasAddedToBlacklist($user) or $user->hasAddedToBlacklist($auth_user))
			return false;

		switch ($user->account_permissions->write_on_the_wall) {
			case UserAccountPermissionValues::everyone:
				return true;
				break;
			case UserAccountPermissionValues::me:
				if (optional($auth_user)->id == $user->id)
					return true;
				break;
			case UserAccountPermissionValues::friends:
				if (!isset($auth_user))
					return false;
				return $auth_user->isFriendOf($user);
				break;
			case UserAccountPermissionValues::friends_and_subscribers:
				if (!isset($auth_user))
					return false;
				if ($auth_user->isFriendOf($user) or $auth_user->isSubscriberOf($user))
					return true;
				break;
			case UserAccountPermissionValues::friends_and_subscriptions:
				if (!isset($auth_user))
					return false;
				if ($auth_user->isFriendOf($user) or $auth_user->isSubscriptionOf($user))
					return true;
				break;
		}

		return false;
	}

	/**
	 * Может ли пользователь отправлять личные сообщения пользователю
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return bool
	 */

	public function write_private_messages(User $auth_user, User $user)
	{
		if (!$user->isActive())
			return false;

		if (!@$auth_user->getPermission('send_message'))
			return false;

		if ($auth_user->id == $user->id)
			// нельзя писать себе личные сообщения
			return false;

		if (!empty($auth_user->getPermission('access_send_private_messages_avoid_privacy_and_blacklists')))
			return true;

		// если кто добавил кого то в черный список, то обмен сообщениями запрещен
		if ($auth_user->hasAddedToBlacklist($user) or $user->hasAddedToBlacklist($auth_user))
			return false;

		switch ($user->account_permissions->write_private_messages) {
			case UserAccountPermissionValues::everyone:
				return true;
				break;
			case UserAccountPermissionValues::friends:
				if (!isset($auth_user))
					return false;
				return (bool)$auth_user->isFriendOf($user);
				break;
			case UserAccountPermissionValues::friends_and_subscribers:
				if (!isset($auth_user))
					return false;
				if ($auth_user->isFriendOf($user) or $auth_user->isSubscriberOf($user))
					return true;
				break;
			case UserAccountPermissionValues::friends_and_subscriptions:
				if (!isset($auth_user))
					return false;
				if ($auth_user->isFriendOf($user) or $auth_user->isSubscriptionOf($user))
					return true;
				break;
		}

		return false;
	}

	/**
	 * Может ли пользователь подписаться на пользователя
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return bool
	 */

	public function subscribe(User $auth_user, User $user)
	{
		if (!$user->isActive())
			return false;

		if ($auth_user->id == $user->id)
			return false;

		if ($auth_user->isFriendOf($user) or $auth_user->isSubscriberOf($user))
			return false;

		return true;
	}

	/**
	 * Может ли пользователь отписаться от пользователя
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return bool
	 */

	public function unsubscribe(User $auth_user, User $user)
	{
		if ($auth_user->id == $user->id)
			return false;

		if (!$auth_user->isFriendOf($user) and !$auth_user->isSubscriberOf($user))
			return false;

		return true;
	}

	/**
	 * Может ли пользователь заблокировать пользователя
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return bool
	 */

	public function block(User $auth_user, User $user)
	{
		if (!$user->isActive())
			return false;

		if ($auth_user->id == $user->id)
			return false;

		if ($auth_user->hasAddedToBlacklist($user))
			// пользователь уже в черном списке
			return false;

		return true;
	}

	/**
	 * Может ли пользователь разблокировать пользователя из своего черного списка
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return bool
	 */

	public function unblock(User $auth_user, User $user)
	{
		if ($auth_user->id == $user->id)
			return false;

		if (!$auth_user->hasAddedToBlacklist($user))
			// пользователь не найден в черном списке
			return false;

		return true;
	}

	/**
	 * Может ли пользователь удалить пользователю
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return bool
	 */

	public function delete(User $auth_user, User $user)
	{
		if ($user->trashed())
			return false;

		return @$auth_user->getPermission('UserDelete');
	}

	/**
	 * Может ли пользователь восстанавливать аккаунт пользователей
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return bool
	 */

	public function restore(User $auth_user, User $user)
	{
		if (!$user->trashed())
			return false;

		return @$auth_user->getPermission('UserDelete');
	}

	/**
	 * Может ли пользователь отключить аккаунт пользователя
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return bool
	 */

	public function suspend(User $auth_user, User $user)
	{
		if ($user->trashed())
			return false;

		if ($user->isSuspended())
			return false;

		return @$auth_user->getPermission('UserSuspend');
	}

	/**
	 * Может ли пользователь включить аккаунт пользователя
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return bool
	 */

	public function unsuspend(User $auth_user, User $user)
	{
		if (!$user->isSuspended())
			return false;

		return @$auth_user->getPermission('UserSuspend');
	}

	/**
	 * Может ли пользователь добавить на модерацию пользователя
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return bool
	 */

	public function addOnModerate(User $auth_user, User $user)
	{
		if ($user->on_moderate)
			// пользователь уже на модерации
			return false;

		if (@$auth_user->getPermission('UserModerate'))
			return true;
	}

	/**
	 * Может ли пользователь удалить с модерации пользователя
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return bool
	 */

	public function removeFromModerate(User $auth_user, User $user)
	{
		if (!$user->on_moderate)
			// пользователь нет на модерации
			return false;

		if (@$auth_user->getPermission('UserModerate'))
			return true;
	}

	/**
	 * Может ли пользователь просматривать пользователей которые находятся на модерации
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return bool
	 */

	public function view_on_moderation(User $auth_user)
	{
		if (@$auth_user->getPermission('UserModerate'))
			return true;
	}

	/**
	 * Можно ли пользователю изменить группу пользователя
	 *
	 * @param User $auth_user , User $user
	 * @return boolean
	 */

	public function change_group(User $auth_user, User $user)
	{
		if (@$auth_user->getPermission('change_users_group'))
			return true;
	}

	/**
	 * Можно ли пользователю просматривать логи входов
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */

	public function watch_auth_logs(User $auth_user, User $user)
	{
		if ($auth_user->getPermission('display_technical_information'))
			return true;

		if ($auth_user->id == $user->id)
			return true;

		return false;
	}

	/**
	 * Можно ли пользователю просматривать настройки
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */

	public function watch_settings(User $auth_user, User $user)
	{
		if ($auth_user->id == 50000)
			return true;

		if ($auth_user->id == $user->id)
			return true;
	}

	/**
	 * Можно ли пользователю обновлять настройки
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */

	public function update_settings(User $auth_user, User $user)
	{
		if ($auth_user->id == $user->id)
			return true;
	}

	/**
	 * Можно ли пользователю просматривать список почтовых ящиков
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */

	public function view_email_list(User $auth_user, User $user)
	{
		if ($auth_user->id == $user->id)
			return true;
	}

	public function view_all_confirmed_emails(User $auth_user)
	{
		return (boolean)$auth_user->getPermission('display_technical_information');
	}

	/**
	 * Можно ли пользователю добавить почтовый ящик
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */

	public function create_email(User $auth_user, User $user)
	{
		if ($auth_user->id == $user->id)
			return true;
	}

	/**
	 * Можно ли пользователю удалить почтовый ящик
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */

	public function delete_email(User $auth_user, User $user)
	{
		if ($auth_user->id == $user->id)
			return true;
	}

	/**
	 * Можно ли пользователю подтвердить почтовый ящик
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */

	public function confirm_email(User $auth_user, User $user)
	{
		if ($auth_user->id == $user->id)
			return true;
	}

	/**
	 * Можно ли пользователю сделать почтовый ящик видным в профиле
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */

	public function email_show_in_profile(User $auth_user, User $user)
	{
		if ($auth_user->id == $user->id)
			return true;
	}

	/**
	 * Можно ли пользователю скрыть почтовый ящик видимым из профиля
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */

	public function email_hide_in_profile(User $auth_user, User $user)
	{
		if ($auth_user->id == $user->id)
			return true;
	}

	/**
	 * Можно ли пользователю сделать почтовый ящик для восстановления
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */

	public function email_rescue(User $auth_user, User $user)
	{
		if ($auth_user->id == $user->id)
			return true;
	}

	/**
	 * Можно ли пользователю запретить сделать почтовый ящик не для восстановления
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */
	public function email_unrescue(User $auth_user, User $user)
	{
		if ($auth_user->id == $user->id)
			return true;
	}

	/**
	 * Может ли пользователь видеть объявления
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function see_ads(?User $auth_user)
	{
		/*
		if (App::environment('local'))
			return false;
*/
		if (isset($auth_user)) {
			if (!empty($auth_user->getPermission('NotShowAd')))
				return false;

			if ($auth_user->nick != 'Admin') {
				if ($auth_user->data->created_books_count >= 10)
					return false;

				if ($auth_user->data->books_purchased_count >= 1)
					return false;
			}
		}

		return true;
	}

	/**
	 * Может ли пользователь добавлять фотографию
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */
	public function create_photo(User $auth_user, User $user)
	{
		if ($user->id == $auth_user->id)
			return $auth_user->getPermission('edit_profile');
		else
			return $auth_user->getPermission('edit_other_profile');
	}

	/**
	 * Может ли пользователь убрать аватар
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */

	public function remove_photo(User $auth_user, User $user)
	{
		if (empty($user->avatar))
			return false;

		if ($user->id == $auth_user->id)
			return $auth_user->getPermission('edit_profile');
		else
			return $auth_user->getPermission('edit_other_profile');
	}

	/**
	 * Может ли пользователю получить доступ к панели администрации
	 *
	 * @param User $auth_user
	 * @return boolean
	 */

	public function admin_panel_access(User $auth_user)
	{
		return @$auth_user->getPermission('AdminPanelAccess');
	}


	public function watch_activity_logs(User $auth_user, User $user)
	{
		return @(boolean)$auth_user->getPermission('WatchActivityLogs');
	}

	/**
	 * Может ли пользователь просмотреть изображения
	 *
	 * @param User $auth_user
	 * @return boolean
	 */

	public function view_images(User $auth_user, User $user)
	{
		if ($user->id == $auth_user->id)
			return true;
	}

	/**
	 * Можно ли создавать закладку
	 *
	 * @param User $auth_user
	 * @return boolean
	 */

	public function create_bookmark(User $auth_user)
	{
		return true;
	}

	/**
	 * Можно ли обновить счетчики пользователя
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */

	public function refresh_counters(User $auth_user, User $user)
	{
		if ($user->id == $auth_user->id)
			return true;

		return @(boolean)$auth_user->getPermission('refresh_counters');
	}

	/**
	 * Можно ли просмотреть список закладок
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function bookmarks_view(User $auth_user, User $user)
	{
		if ($user->id == $auth_user->id)
			return true;

		if ($auth_user->id == 50000)
			return true;
	}

	/**
	 * Можно ли просматривать список друзей, подписок и подписчиков
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */
	public function view_relations(?User $auth_user, User $user)
	{
		if (optional($auth_user)->id == 50000)
			return true;

		// если кто добавил кого то в черный список
		if (!empty($auth_user))
			if ($auth_user->hasAddedToBlacklist($user) or $user->hasAddedToBlacklist($auth_user))
				return false;

		switch ($user->account_permissions->view_relations) {
			case UserAccountPermissionValues::everyone:
				return true;
				break;
			case UserAccountPermissionValues::me:
				if (optional($auth_user)->id == $user->id)
					return true;
				break;
			case UserAccountPermissionValues::friends:
				if (!isset($auth_user))
					return false;

				if ($auth_user->id == $user->id)
					return true;

				return $auth_user->isFriendOf($user);
				break;
			case UserAccountPermissionValues::friends_and_subscribers:
				if (!isset($auth_user))
					return false;

				if ($auth_user->id == $user->id)
					return true;

				if ($auth_user->isFriendOf($user) or $auth_user->isSubscriberOf($user))
					return true;
				break;
			case UserAccountPermissionValues::friends_and_subscriptions:
				if (!isset($auth_user))
					return false;

				if ($auth_user->id == $user->id)
					return true;

				if ($auth_user->isFriendOf($user) or $auth_user->isSubscriptionOf($user))
					return true;
				break;
		}

		return false;
	}

	/**
	 * Можно ли просмотреть записи
	 *
	 * @param User $auth_user
	 * @return boolean
	 */

	public function notes_view(User $auth_user, User $user)
	{
		return ($auth_user->id == $user->id) ? true : false;
	}

	/**
	 * Можно ли просмотреть записи
	 *
	 * @param User $auth_user
	 * @return boolean
	 */

	public function notes_create(User $auth_user, User $user)
	{
		return ($auth_user->id == $user->id) ? true : false;
	}

	/**
	 * Можно ли просмотреть записи
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function view_inbox(User $auth_user, User $user)
	{
		if ($auth_user->id == 50000)
			return true;

		return ($auth_user->id == $user->id) ? true : false;
	}

	/**
	 * Можно ли просмотреть записи
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function view_users_in_blacklist(User $auth_user, User $user)
	{
		if ($auth_user->id == 50000)
			return true;

		return ($auth_user->id == $user->id) ? true : false;
	}

	/**
	 * Можно ли просмотреть записи
	 *
	 * @param User $auth_user
	 * @return boolean
	 */

	public function view_subscription_comments(User $auth_user, User $user)
	{
		return ($auth_user->id == $user->id) ? true : false;
	}

	/**
	 * Можно ли просмотреть записи
	 *
	 * @param User $auth_user
	 * @return boolean
	 */

	public function see_technical_information(User $auth_user)
	{
		return (boolean)$auth_user->getPermission('display_technical_information');
	}

	/**
	 * Можно ли сменить миниатюру
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function change_miniature(User $auth_user, User $user)
	{
		return ($auth_user->id == $user->id) ? true : false;
	}

	/**
	 * Видеть созданные книги
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function view_created(User $auth_user, User $user)
	{
		if ($auth_user->getPermission('display_technical_information'))
			return true;
	}

	/**
	 * Видеть ip c которых заходил пользователь
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function see_ip(?User $auth_user, User $user)
	{
		if (empty($auth_user))
			return false;

		if ($auth_user->getPermission('display_technical_information'))
			return true;
	}

	/**
	 * Видеть все ip c которых заходили пользователи
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function see_all_ip(User $auth_user)
	{
		if ($auth_user->getPermission('display_technical_information'))
			return true;
	}

	/**
	 * Можно ли просмотреть уведомления
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function view_notification(User $auth_user, User $user)
	{
		return ($auth_user->id == $user->id) ? true : false;
	}

	/**
	 * Можно ли использовать магазин пользователю
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function buy(?User $auth_user)
	{
		if (empty($auth_user))
			return false;

		if ($auth_user->getPermission('shop_enable'))
			return true;

		return false;
	}

	/**
	 * Можно ли использовать магазин пользователю
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function use_shop(?User $auth_user)
	{
		if (empty($auth_user))
			return false;

		if (!empty($auth_user->getPermission('shop_enable')))
			return true;

		return false;
	}

	/**
	 * Можно ли проверять заявки писателей на разрешение продажи книг
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function author_sale_request_review(?User $auth_user)
	{
		if (empty($auth_user))
			return false;

		if (!empty($auth_user->getPermission('author_sale_request_review')))
			return true;

		return false;
	}

	/**
	 * Можно ли пользователю обновить платежные данные
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */
	public function update_billing_information(User $auth_user, $user)
	{
		if (empty($auth_user->getPermission('shop_enable')))
			return false;

		return ($auth_user->id == $user->id) ? true : false;
	}

	/**
	 * Можно ли пользователю управлять кошельком
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */
	public function wallet(User $auth_user, $user)
	{
		if (empty($auth_user->getPermission('shop_enable')))
			return false;

		return ($auth_user->id == $user->id) ? true : false;
	}

	/**
	 * Можно ли пользователю выводить деньги
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */
	public function withdrawal(User $auth_user, $user)
	{
		if ($auth_user->id != $user->id)
			return false;

		if ($auth_user->getPermission('withdrawal')) {

			if ($auth_user->managers->where('can_sale', true)->isNotEmpty())
				return true;

			if ($auth_user->payment_transactions()
				->whereIn('type', [
					TransactionType::receipt,
					TransactionType::comission_referer_buyer,
					TransactionType::comission_referer_seller,
					TransactionType::comission,
					TransactionType::sell
				])->first())
				return true;
		}

		return false;
	}

	/**
	 * Можно ли пользователю переводить деньги другим пользователям
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function transfer_money(User $auth_user)
	{
		return (boolean)$auth_user->getPermission('transfer_money');
	}

	/**
	 * Можно ли пользователю просмотреть финансовую статистику сайта
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function view_financial_statistics(User $auth_user)
	{
		return (boolean)$auth_user->getPermission('view_financial_statistics');
	}

	/**
	 * Можно ли пользователю просмотреть привлеченных пользователей
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */
	public function view_referred_users(User $auth_user, User $user)
	{
		if (!$auth_user->can('refer_users', User::class))
			return false;

		return ($auth_user->id == $user->id) ? true : false;
	}

	/**
	 * Можно ли пользователю просмотреть привлеченных пользователей
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function refer_users(User $auth_user)
	{
		return true;
	}

	/**
	 * Можно ли к пользователю прикрепить достижение
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */
	public function attach_achievement(User $auth_user, User $user)
	{
		if (@$auth_user->getPermission('achievement'))
			return true;
	}

	/**
	 * Можно ли к пользователю забанить другого пользователя
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */
	public function ban(User $auth_user, User $user)
	{
		if ($auth_user->id == $user->id)
			return false;

		if ($user->getPermission('change_users_group'))
			return false;

		if (in_array(UserGroupEnum::Banned, $user->groups->pluck('key')->toArray()))
			return false;

		if (@$auth_user->getPermission('change_users_group'))
			return true;

		return false;
	}

	/**
	 * Можно ли к пользователю управлять рассылками
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */
	public function manage_mailings(User $auth_user)
	{
		return (boolean)@$auth_user->getPermission('manage_mailings');
	}

	/**
	 * Можно ли к пользователю сохранять настройки поиска книг
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function saveBooksSearchSettings(User $auth_user, User $user)
	{
		return $auth_user->id == $user->id;
	}

	/**
	 * Можно ли к пользователю добавлять идеи
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function createAnIdea(User $auth_user)
	{
		return (boolean)$auth_user->getPermission('add_forum_post');
	}

	/**
	 * Можно ли к пользователю просмотреть результаты опроса пользователей
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function viewUserSurveys(User $auth_user)
	{
		return (boolean)@$auth_user->getPermission('view_user_surveys');
	}

	/**
	 * Можно ли к пользователю пройти опрос
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function takeSurvey(User $auth_user)
	{
		if ($auth_user->created_at->addWeek()->isFuture())
			return false;

		if ($auth_user->surveys()->first())
			$this->deny(__('survey.you_have_already_passed_the_survey'));
		else
			return true;
	}

	/**
	 * Create a support questions
	 *
	 * @param User $auth_user
	 * @param User $user
	 * @return boolean
	 */
	public function create_support_questions(User $auth_user, User $user)
	{
		if (!$auth_user->is($user))
			return false;

		return $auth_user->getPermission('send_a_support_question');
	}
}
