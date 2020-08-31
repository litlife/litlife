<?php

namespace App\Http\Controllers;

use App\Forum;
use App\ForumGroup;
use App\Http\Requests\StoreForum;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ForumController extends Controller
{
	/**
	 * Форум
	 *
	 * @return View
	 */
	public function index()
	{
		$forumGroups = ForumGroup::orderBySettings()->get();

		$forumGroups->load(['forums' => function ($query) use ($forumGroups) {

			$order = [];
			foreach ($forumGroups as $forum_group) {
				$sort = unserialize($forum_group->forum_sort);

				if (is_array($sort))
					$order = array_merge($order, $sort);
			}

			if (!empty($order))
				$query->orderByField('id', $order);

		}, 'forums.users_with_access',
			'forums.last_post',
			'forums.last_post.create_user.avatar',
			'forums.last_topic',
			'forums.user_access'
		]);

		return view('forum.index', compact('forumGroups'));
	}

	/**
	 * Форма создания нового форума
	 *
	 * @param Request $request
	 * @return View
	 * @throws
	 */
	public function create(Request $request)
	{
		$this->authorize('create', Forum::class);

		if (!empty($request->forum_group_id))
			$forum_group = ForumGroup::findOrFail($request->forum_group_id);

		return view('forum.forum.create', compact('forum_group'));
	}

	/**
	 * Сохранение нового форума
	 *
	 * @param Request $request
	 * @return Response
	 * @throws
	 */

	public function store(StoreForum $request)
	{
		$this->authorize('create', Forum::class);

		$forum = new Forum($request->all());

		if (!empty($request->forum_group_id)) {
			$forum_group = ForumGroup::findOrFail($request->forum_group_id);
			$forum_group->forums()->save($forum);

			if ($request->private) {
				$private_users = $request->input('private_users') ?? [];
				array_push($private_users, $forum->create_user_id);
				$forum->users_with_access()->sync($private_users);
			}

			return redirect()->route('forums.index');
		} else {
			return redirect()->back();
		}
	}

	/**
	 * Вывод форума и списка тем
	 *
	 * @param Forum $forum
	 * @return View
	 * @throws
	 */

	public function show(Forum $forum)
	{
		$this->authorize('view', $forum);

		$topics = $forum->topics()
			->with(['last_post', 'forum'])
			->unarchived()
			->orderBy('forum_priority', 'desc')
			->when($forum->isIdeaForum(), function ($query) {
				$query->select('topics.*', 'posts.like_count')
					->leftJoin('posts', 'topics.top_post_id', '=', 'posts.id')
					->orderForIdeaForum();
			})
			->orderBy('last_post_created_at', 'desc')
			->paginate();

		TwitterCard::setTitle($forum->name);

		if (!empty($forum->description)) {
			TwitterCard::setDescription($forum->description);
			OpenGraph::setDescription($forum->description);
			SEOMeta::setDescription($forum->description);
		}

		if (request()->ajax())
			return view('forum.show', compact('forum', 'topics'))->renderSections()['content'];
		else
			return view('forum.show', compact('forum', 'topics'));
	}

	/**
	 * Форма редактирования
	 *
	 * @param Forum $forum
	 * @return View
	 * @throws
	 */

	public function edit(Forum $forum)
	{
		$this->authorize('update', $forum);

		return view('forum.forum.edit', compact('forum'));
	}

	/**
	 * Сохранение отредактированного
	 *
	 * @param StoreForum $request
	 * @param Forum $forum
	 * @return Response
	 * @throws
	 */

	public function update(StoreForum $request, Forum $forum)
	{
		$this->authorize('update', $forum);

		$forum->fill($request->all());
		$forum->user_edited_at = now();
		$forum->save();

		if ($forum->isPrivate()) {
			$private_users = $request->input('private_users') ?? [];
			array_push($private_users, auth()->id());
			$forum->users_with_access()->sync($private_users);
		} else {
			$forum->users_with_access()->detach();
		}

		return back();
	}

	/**
	 * Удаление и восстановление
	 *
	 * @param int $id
	 * @return Response
	 * @throws
	 */

	public function destroy($id)
	{
		$forum = Forum::any()->findOrFail($id);

		if ($forum->trashed()) {
			$this->authorize('restore', $forum);

			$forum->restore();
		} else {
			$this->authorize('delete', $forum);

			$forum->delete();
		}

		return $forum;
	}

	/**
	 * Поиск js
	 *
	 * @param Request $request
	 * @return array
	 */

	public function search(Request $request)
	{
		$str = trim($request->input('q'));

		$query = Forum::void();

		if (is_numeric($str)) {
			$query->where('id', $str);
		} else {
			$query->fulltextSearch($str);
		}

		$items = $query->limit(10)->get();

		return ['items' => $items];
	}
}
