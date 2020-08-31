<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Book;
use App\Comment;
use App\Complain;
use App\Http\Requests\StoreComplain;
use App\Post;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ComplainController extends Controller
{
	/**
	 * Список жалоб
	 *
	 * @return View
	 */
	public function index()
	{
		$complains = Complain::latest()
			->orderStatusChangedDesc()
			->with([
				'create_user',
				'status_changed_user',
				'complainable' => function (MorphTo $morphTo) {
					$morphTo->morphWith([
						Comment::class => [
							"create_user.avatar",
							'originCommentable.authors.managers',
							'create_user.latest_user_achievements.achievement.image',
							'create_user.groups',
							"userBookVote",
							'votes' => function ($query) {
								$query->where("create_user_id", auth()->id());
							}
						],
						Post::class => [
							'create_user.latest_user_achievements.achievement.image',
							"edit_user",
							"topic",
							'forum',
							"create_user.groups",
							'likes' => function ($query) {
								$query->where('create_user_id', auth()->id());
							}],
						Blog::class => [
							'create_user.account_permissions',
							'create_user.avatar',
							'likes' => function ($query) {
								$query->where('create_user_id', auth()->id());
							},
							'owner' => function ($query) {
								$query->any();
							}
						],
						Book::class => [
							'authors.managers',
							'genres',
							'sequences'
						],
					]);
				},
				'complainable.create_user'
			])
			->simplePaginate();

		return view('complain.index', compact('complains'));
	}

	/**
	 * Отображение жалобы
	 *
	 * @param Complain $complain
	 * @return View
	 */
	public function show(Complain $complain)
	{
		$this->authorize('view', $complain);

		return view('complain.show', compact('complain'));
	}

	public function getItem($type, $id)
	{
		$map = Relation::morphMap();

		if (!isset($map[$type]))
			abort(404, 'Model ' . $type . ' not found');
		else
			$model = $map[$type];

		return $model::findOrFail($id);

		//$this->complain = $this->item->complaints()->where(['user_id' => Auth::id()])->first();
	}

	/**
	 * Форма создания или редактирование
	 *
	 * @param string $type
	 * @param int $id
	 * @return View
	 * @throws
	 */
	public function create_edit($type, $id)
	{
		$this->authorize('create', Complain::class);

		$item = $this->getItem($type, $id);

		$this->authorize('complain', $item);

		$complain = $item->complaints()->where(['create_user_id' => auth()->id()])->first() ?? [];

		return view('complain.create_edit', compact('item', 'type', 'id', 'complain'));
	}

	/**
	 * Сохранение
	 *
	 * @param StoreComplain $request
	 * @param string $type
	 * @param int $id
	 * @return Response
	 * @throws
	 */
	public function save(StoreComplain $request, $type, $id)
	{
		$this->authorize('create', Complain::class);

		$item = $this->getItem($type, $id);

		$this->authorize('complain', $item);

		$complain = $item->complaints()->where(['create_user_id' => auth()->id()])->first();

		if (empty($complain)) {
			$complain = new Complain();
			$complain->complainable_type = $type;
			$complain->complainable_id = $item->id;
		}

		$complain->fill($request->all());
		$complain->save();

		Complain::flushCachedOnModerationCount();

		if ($complain->wasRecentlyCreated)
			return redirect()
				->route('complaints.show', $complain->id)
				->with(['success' => __('complain.complaint_sent')]);
		else
			return redirect()
				->route('complaints.show', $complain->id)
				->with(['success' => __('complain.complaint_was_successfully_edited')]);
	}

	/**
	 * Отметить жалобу как проверенную
	 *
	 * @param Complain $complain
	 * @return Response
	 * @throws
	 */
	public function check(Complain $complain)
	{
		$this->authorize('approve', $complain);

		$complain->statusAccepted();
		$complain->save();

		Complain::flushCachedOnModerationCount();

		if (request()->ajax())
			return view('complain.status', ['item' => $complain]);
		else
			return redirect()
				->route('complaints.index')
				->with(['success' => __('complain.checked')]);
	}

	/**
	 * Начать рассматривать жалобу
	 *
	 * @param Complain $complain
	 * @return Response
	 * @throws
	 */
	public function startReview(Complain $complain)
	{
		$this->authorize('startReview', $complain);

		$complain->statusReviewStarts();
		$complain->save();

		Complain::flushCachedOnModerationCount();

		if (request()->ajax())
			return view('complain.status', ['item' => $complain]);
		else
			return redirect()
				->route('complaints.index')
				->with(['success' => __('complain.you_have_begun_to_review_the_complaint')]);
	}

	/**
	 * Прекратить рассматривать жалобу
	 *
	 * @param Complain $complain
	 * @return Response
	 * @throws
	 */
	public function stopReview(Complain $complain)
	{
		$this->authorize('stopReview', $complain);

		$complain->statusSentForReview();
		$complain->save();

		Complain::flushCachedOnModerationCount();

		if (request()->ajax())
			return view('complain.status', ['item' => $complain]);
		else
			return redirect()
				->route('complaints.index')
				->with(['success' => __('complain.you_reject_to_review_the_complaint')]);
	}
}
