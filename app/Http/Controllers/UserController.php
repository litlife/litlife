<?php

namespace App\Http\Controllers;

use App\Achievement;
use App\AchievementUser;
use App\Blog;
use App\Enums\UserGroupEnum;
use App\Http\Requests\StoreUser;
use App\Http\SearchResource\CollectionSearchResource;
use App\Image;
use App\Notifications\GroupAssignmentNotification;
use App\User;
use App\UserAuthLog;
use App\UserGroup;
use App\UserOnModeration;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
	/**
	 * Профиль
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function show(User $user)
	{
		if (!empty($user->setting)) {
			$top_blog_record = $user->setting->top_blog_record;
		}

		$user->loadMissing('latest_user_achievements.achievement.image', 'account_permissions', 'avatar');

		$blogs = $user->blog()
			->with('create_user.avatar')
			->when($top_blog_record, function ($query, $top_blog_record) {
				$query->where('id', '!=', $top_blog_record->id);
			})
			->roots()
			->latest()
			->simplePaginate();

		foreach ($blogs as $blog) {
			$blog->setRelation('owner', $user);
		}

		$blogs->loadMissing(['likes' => function ($query) {
			$query->where('create_user_id', auth()->id());
		}]);

		if (request()->input('blog')) {
			$blog = Blog::findOrFail(intval(request()->input('blog')));

			if ($blog->level > 0) {
				$blog = $blog->root;
			}

			$descendants = $user->blog()->with("create_user.avatar")
				->descendants($blog->id)
				->oldest()
				->get();

			foreach ($descendants as $blog) {
				$blog->setRelation('owner', $user);
			}

			$descendants->loadMissing(['likes' => function ($query) {
				$query->where('create_user_id', auth()->id());
			}]);
		}

		/*
		$descendants = $user->blog()
			->with("create_user.avatar", "owner.setting", 'owner.avatar')
			->descendants($blogs->pluck('id')->toArray())
			->oldest()
			->get();
  */

		if (request()->ajax()) {
			return view('blog.index', [
				'user' => $user,
				'blogs' => $blogs,
				'descendants' => $descendants ?? null
			])->render();
		}

		if (isset($top_blog_record)) {
			$top_blog_record->load(['likes' => function ($query) {
				$query->where('create_user_id', auth()->id());
			}]);
		}

		$managers = $user->managers()
			->with(['manageable' => function ($query) {
				$query->any();
			}])
			->where('manageable_type', 'author')
			->accepted()
			->get();

		OpenGraph::setType('profile')
			->setTitle($user->userName)
			->setProfile([
				'first_name' => $user->first_name,
				'last_name' => $user->last_name,
				'username' => $user->nick,
				'gender' => $user->gender
			]);

		TwitterCard::setTitle($user->userName);

		if (!empty($user->avatar)) {
			OpenGraph::addImage($user->avatar->url,
				[
					'width' => $user->avatar->getWidth(),
					'height' => $user->avatar->getHeight()
				]);

			TwitterCard::setImage($user->avatar->fullUrlMaxSize(900, 900));
		}

		if (auth()->check() and auth()->user()->can('view_all_confirmed_emails', User::class)) {
			$emails = $user->emails()->confirmed()->get() ?? null;
		} else {
			$emails = $user->emails()->confirmed()->showedInProfile()->get() ?? null;
		}

		if (auth()->check() and auth()->user()->can('see_ip', $user)) {
			$last_auth_log = $user->auth_logs()->latest()->limit(1)->get()->first() ?? null;
		}

		$array = [
			'user' => $user,
			'blogs' => $blogs,
			'descendants' => $descendants ?? null,
			'top_blog_record' => $top_blog_record ?? null,
			'managers' => $managers,
			'emails' => $emails,
			'last_auth_log' => $last_auth_log ?? null
		];

		if ($user->trashed())
			return response()->view('user.show.trashed', $array, 404);
		elseif ($user->isSuspended())
			return response()->view('user.show.suspended', $array);
		else
			return response()->view('user.show.show', $array);
	}

	/**
	 * Форма редактирования
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function edit(User $user)
	{
		$this->authorize('update', $user);

		return view('user.edit', compact('user'));
	}

	/**
	 * Сохранение
	 *
	 * @param StoreUser $request
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function update(StoreUser $request, User $user)
	{
		$this->authorize('update', $user);

		$user->fill($request->validated());
		$user->user_edited_at = now();
		$user->name_show_type = $request['name_show_type'];
		$user->save();

		if (!empty($request->data) and is_array($request->data)) {
			$user->data->fill($request->data);
			$user->data->save();
		}

		return back()
			->with(['success' => __('user.profile_edit_success')]);
	}

	/**
	 * Форма изменения группы
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function groupEdit(User $user)
	{
		$this->authorize('change_group', $user);

		$groups = UserGroup::all();

		$user_groups = $user->groups()
			->get()
			->pluck('id')
			->toArray();

		return view('user.change_group', compact('user', 'groups', 'user_groups'));
	}

	/**
	 * Назначить группу пользователей
	 *
	 * @param Request $request
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function groupUpdate(Request $request, User $user)
	{
		$this->authorize('change_group', $user);

		$this->validate($request, [
			'groups_id' => 'required|array|exists:user_groups,id',
			'text_status' => 'sometimes|string|nullable'
		], [], trans('user'));

		DB::transaction(function () use ($user, $request) {

			$groups_id = $request->input('groups_id');

			$oldUserGroups = $user->groups()->get();

			$groups = UserGroup::find($groups_id);

			$user->groups()->sync($groups->pluck('id')->toArray());
			$user->text_status = $request->text_status;

			$fillteredUserGroups = $groups->diff($oldUserGroups);

			$user->notify(new GroupAssignmentNotification($fillteredUserGroups));

			foreach ($groups as $group)
				$user->removeTextStatus($group->name);

			$user->save();
		});

		return redirect()->route('users.groups.edit', ['user' => $user]);
	}


	/**
	 * Добавить на модерацию
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function addOnModeration(User $user)
	{
		$this->authorize('addOnModerate', $user);

		$userOnModerate = new UserOnModeration;
		$userOnModerate->user_id = $user->id;
		$userOnModerate->user_adds_id = Auth::id();
		$userOnModerate->save();

		return back();
	}

	/**
	 * Удаление с модерации
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function removeFromModeration(User $user)
	{
		$this->authorize('removeFromModerate', $user);

		UserOnModeration::where('user_id', $user->id)
			->delete();

		return back();
	}

	/**
	 * Обновление счетчиков
	 *
	 * @param User $user
	 * @return Response
	 */
	public function refreshCounters(User $user)
	{
		Artisan::call('refresh:user_counters', ['id' => $user->id]);

		return redirect()
			->route('profile', ['user' => $user]);
	}

	/**
	 * Логи успешных входов
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function authLogs(User $user)
	{
		$this->authorize('watch_auth_logs', $user);

		$auth_logs = $user->auth_logs()
			->latest()
			->with('user_agent')
			->simplePaginate();

		return view('user.auth_logs.succeed', compact('user', 'auth_logs'));
	}

	/**
	 * Логи проваленных входов
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function authFails(User $user)
	{
		$this->authorize('watch_auth_logs', $user);

		$auth_fails = $user->auth_fails()
			->latest()
			->with('user_agent')
			->simplePaginate();

		return view('user.auth_logs.failed', compact('user', 'auth_fails'));
	}

	/**
	 * Поиск пользователя
	 *
	 * @param Request $request
	 * @return array
	 */
	public function search(Request $request)
	{
		$str = trim($request->input('q'));

		$query = User::void();

		if (is_numeric($str)) {
			$query->where('id', $str);
		} else {
			$query->fulltextSearch($str);
		}

		$items = $query->limit(10)->get();

		return ['items' => $items];
	}

	/**
	 * Отключить аккаунт
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function suspend(User $user)
	{
		$this->authorize('suspend', $user);

		$user->suspended_at = now();
		$user->save();

		return back()
			->with(['success' => __('user.suspended')]);
	}

	/**
	 * Включить аккаунт
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function unsuspend(User $user)
	{
		$this->authorize('unsuspend', $user);

		$user->suspended_at = null;
		$user->save();

		return back()
			->with(['success' => __('user.unsuspended')]);
	}

	/**
	 * Удалить аккаунт
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function delete(User $user)
	{
		$this->authorize('delete', $user);

		$user->delete();

        activity()->performedOn($user)
            ->log('deleted');

		return back()
			->with(['success' => __('user.deleted')]);
	}

	/**
	 * Восстановить аккаунт
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function restore(User $user)
	{
		if (!$user->trashed())
			return redirect()->route('profile', $user);

		$this->authorize('restore', $user);

		$user->restore();

        activity()->performedOn($user)
            ->log('restored');

		return back()
			->with(['success' => __('user.restored')]);
	}

	/**
	 * Список достижений пользователя
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function achievements(User $user)
	{
		$user_achievements = $user->user_achievements()
			->with('achievement.image')
			->latest()
			->simplePaginate();

		return view('user.achievements', compact('user', 'user_achievements'));
	}

	/**
	 * Прикрепить достижение к пользователю
	 *
	 * @param Request $request , User $user
	 * @return Response
	 * @throws
	 */
	public function attach_achievement(Request $request, User $user)
	{
		$this->authorize('attach', Achievement::class);

		$this->validate($request, ['achievement' => 'required|integer|exists:achievements,id']);

		$achievement = Achievement::findOrFail($request->achievement);

		$user_achievement = AchievementUser::where('user_id', $user->id)
			->where('achievement_id', $achievement->id)
			->first();

		if (empty($user_achievement)) {
			$user_achievement = new AchievementUser;
			$user_achievement->user_id = $user->id;
			$user_achievement->achievement_id = $achievement->id;
			$user_achievement->created_at = now();
			$user_achievement->updated_at = now();
			$user_achievement->save();
		}

		return back();
	}

	/**
	 * Открепить достижение от пользователя
	 *
	 * @param User $user
	 * @param Achievement $achievement
	 * @return Response
	 * @throws
	 */
	public function detach_achievement(User $user, Achievement $achievement)
	{
		$this->authorize('detach', Achievement::class);

		$user->user_achievements()
			->where('achievement_id', $achievement->id)
			->first()
			->delete();

		return back();
	}

	/**
	 * Просмотр действий
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function activity_logs(User $user)
	{
		$activityLogs = $user->actions()
			->latest()
			->simplePaginate();

		$activityLogs->load(['causer', 'subject' => function ($query) {
			$query->any();
		}]);

		return view('activity_log.index', compact('activityLogs'));
	}

	/**
	 * Изображения пользователя
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function images(User $user)
	{
		$this->authorize('view_images', $user);

		$images = $user->images()->latest()
			->simplePaginate();

		foreach ($images as $image) {
			$image->maxWidth = 150;
			$image->maxHeight = 100;
		}

		return view('user.image', compact('images'));
	}

	/**
	 *
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function managers(User $user)
	{
		//$this->authorize('view_images', $user);

		$managers = $user->managers()
			->with(['manageable', 'create_user', 'user'])
			->paginate();

		return view('user.managers', compact('managers'));
	}

	/**
	 *
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function setMiniature(Request $request, User $user)
	{
		$this->authorize('change_miniature', $user);

		$data = $request->validate([
			'width' => 'required|integer',
			'height' => 'required|integer',
			'x' => 'required|integer',
			'y' => 'required|integer'
		]);

		$stream = $user->avatar->getStream();

		$img = \Intervention\Image\Facades\Image::make($stream);
		$img->crop($request->width, $request->height, $request->x, $request->y);

		if (!empty($user->miniature)) {
			$user->miniature->delete();
		}

		$image = new Image;
		$image->openImage($img->getCore());
		$image->storage = config('filesystems.default');
		$image->name = 'miniature.jpg';
		$image->save();

		$user->miniature_image_id = $image->id;
		$user->save();

	}

	public function allAuthLogs()
	{
		$this->authorize('see_all_ip', User::class);

		$ip = request()->ip;

		$auth_logs = UserAuthLog::with(['user', 'user_agent']);

		if (!empty($ip))
			$auth_logs->whereIp($ip);

		$auth_logs = $auth_logs->latest()->simplePaginate();

		return view('user.all_auth_logs', ['auth_logs' => $auth_logs, 'ip' => $ip ?? null]);
	}

	public function refer(User $user)
	{
		$this->authorize('refer_users', User::class);

		$ref_name = config('litlife.name_user_refrence_get_param');
		$comission_from_refrence_buyer = config('litlife.comission_from_refrence_buyer');
		$comission_from_refrence_seller = config('litlife.comission_from_refrence_seller');

		return view('user.refer', [
			'user' => $user,
			'ref_name' => $ref_name,
			'comission_from_refrence_buyer' => $comission_from_refrence_buyer,
			'comission_from_refrence_seller' => $comission_from_refrence_seller,
		]);
	}

	public function createdCollections(User $user)
	{
		$builder = $user
			->created_collections();

		$resource = (new CollectionSearchResource(request(), $builder));
		$vars = $resource->getVars();

		$vars['user'] = $user;
		$vars['collections'] = $resource->getQuery()->simplePaginate();

		if (request()->ajax())
			return view('collection.list', $vars);

		return view('user.collection.created', $vars);
	}

	public function favoriteCollections(User $user)
	{
		$builder = $user
			->favorite_collections();

		$resource = (new CollectionSearchResource(request(), $builder));
		$vars = $resource->getVars();

		$vars['user'] = $user;
		$vars['collections'] = $resource->getQuery()->simplePaginate();

		if (request()->ajax())
			return view('collection.list', $vars);

		return view('user.collection.favorite', $vars);
	}

	public function searchUserWithNick(Request $request)
	{
		$str = trim($request->input('nick'));

		$query = User::whereNickEquals($str);

		return $query->limit(1)->get();
	}

	/**
	 * Забанить пользователя
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function ban(User $user)
	{
		$this->authorize('change_group', $user);

		$group = UserGroup::where('key', UserGroupEnum::Banned)->first();

		$user->groups()->sync([$group->id]);
		$user->save();

		return redirect()
			->route('profile', $user)
			->with(['success' => __('user.user_is_banned')]);
	}

	/**
	 * Аватар пользователя в полном размере
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function avatar(User $user)
	{
		if ($user->trashed() or empty($user->avatar) or $user->avatar->trashed())
			abort(404);

		return view('user.avatar.show', ['user' => $user]);
	}

	/**
	 * Support questions
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function supportRequests(User $user)
	{
		$this->authorize('create_support_questions', $user);

		$supportQuestions = $user->createdSupportQuestions()
			->with('create_user', 'latest_message.create_user')
			->orderBy('last_message_created_at', 'desc')
			->simplePaginate();

		if ($supportQuestions->count() < 1)
			return redirect()
				->route('support_questions.create', ['user' => $user]);

		return view('user.support_question.index', ['user' => $user, 'supportQuestions' => $supportQuestions]);
	}
}
