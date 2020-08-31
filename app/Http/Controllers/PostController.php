<?php

namespace App\Http\Controllers;

use App\Events\ForumCountOfPostsHasChanged;
use App\Events\TopicCountOfPostsHasChanged;
use App\Http\Requests\StorePost;
use App\Jobs\Forum\UpdateForumCounters;
use App\Jobs\Mail\NewForumReplyNotificationJob;
use App\Jobs\Topic\UpdateTopicCounters;
use App\Post;
use App\Topic;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PostController extends Controller
{
	/**
	 * Форма создания сообщения или ответа
	 *
	 * @param Topic $topic
	 * @param Request $request
	 * @return View
	 * @throws
	 */

	public function create(Topic $topic, Request $request)
	{
		$this->authorize('create_post', $topic);

		if ($request->parent) {
			$parent = Post::findOrFail($request->parent);

			$this->authorize('reply', $parent);
		}

		$data = [
			'topic' => $topic,
			'parent' => $parent ?? null,
			'level' => isset($parent->level) ? $parent->level + 1 : 0
		];

		if (request()->ajax())
			return view('forum.post.create_form', $data);
		else
			return view('forum.post.create', $data);
	}

	/**
	 * Сохранение сообщения
	 *
	 * @param StorePost $request
	 * @param Topic $topic
	 * @return mixed
	 * @throws
	 */
	public function store(StorePost $request, Topic $topic)
	{
		$this->authorize('create_post', $topic);

		if ($request->parent) {
			$parent = Post::findOrFail($request->parent);

			$this->authorize('reply', $parent);
		}

		if (auth()->user()->posts()->where('created_at', '>', now()->subMinutes(10))->count() >= 10)
			return back()->withErrors(['bb_text' => __('post.you_comment_to_fast')]);
		/*
				if (auth()->user()->posts()
					->where('bb_text', $request->bb_text)
					->latest()
					->limit(2)
					->count() >= 2)
					return back()->withErrors(['bb_text' => __('post.you_leave_same_posts')]);
		*/
		$post = new Post($request->all());

		if (isset($parent->id))
			$post->parent = $parent;

		$post = $topic->posts()->save($post);

		if (request()->ajax())
			return $post;
		else
			return redirect()
				->route('posts.go_to', $post);
	}


	/**
	 * Show one message
	 * @param Post $post
	 * @return View
	 * @throws
	 */

	public function show(Post $post)
	{
		return view('forum.post.item.default', ['item' => $post, 'parent' => $post->parent ?? null, 'level' => $post->level]);
	}

	/**
	 * Форма редактирования
	 *
	 * @param Post $post
	 * @return View
	 * @throws
	 */

	public function edit(Post $post)
	{
		$this->authorize('update', $post);

		if (request()->ajax())
			return view('forum.post.edit_form', compact('post'));
		else
			return view('forum.post.edit', compact('post'));
	}

	/**
	 * Сохранение сообщения
	 *
	 * @param StorePost $request
	 * @param Post $post
	 * @return Response
	 * @throws
	 */

	public function update(StorePost $request, Post $post)
	{
		$this->authorize('update', $post);

		DB::beginTransaction();

		$post->fill($request->all());
		$post->user_edited_at = now();
		$post->edit_user_id = auth()->id();
		$post->save();

		DB::commit();

		if (request()->ajax())
			return $post;
		else
			return redirect()
				->route('posts.go_to', $post);
	}

	/**
	 * Удаляет или восстанавливает пост
	 *
	 * @param int $id
	 * @return Response
	 * @throws
	 */

	public function destroy($id)
	{
		$post = Post::any()->findOrFail($id);

		if ($post->trashed()) {
			$this->authorize('restore', $post);

			$post->restore();
		} else {
			$this->authorize('delete', $post);

			$post->delete();
		}

		return $post;
	}

	/**
	 * Делает редирект к нужному посту в теме
	 *
	 * @param Post $post
	 * @return Response
	 */

	public function go_To($post)
	{
		$post = Post::acceptedAndSentForReview()
			->findOrFail($post);

		$topic = $post->topic ?? abort(404);

		$forum = $post->forum ?? abort(404);

		$page = $post->getTopicPage();

		return redirect()
			->route('topics.show', [
				'topic' => $topic,
				'post' => $post->id,
				'page' => $page,
				'#item_' . $post->id
			]);
	}

	/**
	 * Загрузка ответов на сообщения на форуме
	 *
	 * @param int $id
	 * @return View
	 */

	public function descendants($id)
	{
		$post = Post::findOrFail($id);

		$descendants = Post::acceptedOrBelongsToAuthUser()
			->with('create_user.latest_user_achievements.achievement.image', 'create_user.groups', "edit_user", "topic")
			->descendants($post->id)
			->orderBy("created_at", "asc")
			->get();

		$descendants->load(['likes' => function ($query) {
			$query->where('create_user_id', auth()->id());
		}]);

		$level = request()->level ?? null;
		$level++;

		return view('forum.post.childs', compact('post', 'descendants', 'level'));
	}

	/**
	 * Закрепляет пост
	 *
	 * @param Post $post
	 * @return Response
	 * @throws
	 */

	public function fix(Post $post)
	{
		$this->authorize('fix', $post);

		$post->fix();

		return back();
	}

	/**
	 * Открепляет пост
	 *
	 * @param Post $post
	 * @return Response
	 * @throws
	 */

	public function unfix(Post $post)
	{
		$this->authorize('unfix', $post);

		$post->unfix();

		return back();
	}

	/**
	 * Форма перемещения постов
	 *
	 * @return View
	 * @throws
	 */

	public function move()
	{
		$this->authorize('move', Post::class);

		return view('forum.post.move', ['ids' => explode(',', request()->input('ids'))]);
	}

	/**
	 * Перемещение постов
	 *
	 * @param Request $request
	 * @return Response
	 * @throws
	 */

	public function transfer(Request $request)
	{
		$this->authorize('move', Post::class);

		$this->validate($request, [
			'posts' => 'required|array|exists:posts,id',
			'topic_id' => 'required|numeric|exists:topics,id'
		]);

		$posts = Post::find($request->input('posts'));

		$topic = Topic::findOrFail($request->input('topic_id'));

		DB::transaction(function () use ($posts, $topic) {

			$ids = $posts->pluck('id')->toArray();

			// перемещаем посты
			Post::whereIn('id', $ids)
				->update(['topic_id' => $topic->id, 'forum_id' => $topic->forum->id]);

			// перемещаем их ответы
			Post::descendants($ids)
				->update(['topic_id' => $topic->id, 'forum_id' => $topic->forum->id]);

			UpdateTopicCounters::dispatch($topic);
			UpdateForumCounters::dispatch($topic->forum);

			$topic_ids = $posts->pluck('topic_id')->unique();

			Topic::with('top_post')
				->find($topic_ids)
				->each(function ($topic) {

					if (!empty($topic->top_post_id)) {
						if (empty($topic->top_post) or $topic->top_post->topic_id != $topic->id)
							$topic->top_post_id = null;
					}

					$topic->postsCountRefresh();
					$topic->lastPostRefresh();
					$topic->save();

					$topic->forum->topicsCountRefresh();
					$topic->forum->postsCountRefresh();
					$topic->forum->lastPostRefresh();
					$topic->forum->save();
				});
		});

		return redirect()
			->route('topics.show', ['topic' => $topic]);
	}

	/**
	 * Поиск сообщения по id
	 *
	 * @param Request $request
	 * @return array
	 */

	public function search(Request $request)
	{
		$items = Post::where('id', $request->input('q'))
			->limit(10)->get();

		return ['items' => $items];
	}

	/**
	 * Сообщения на проверке
	 *
	 * @param
	 * @param
	 * @return View
	 */

	public function onCheck()
	{
		$posts = Post::sentOnReview()->get();

		return view('forum.post.on_check', compact('posts'));
	}

	/**
	 * Одобрение сообщения
	 *
	 * @param int $id
	 * @return Post $post
	 * @throws
	 */

	public function approve($id)
	{
		$post = Post::any()->findOrFail($id);

		$this->authorize('approve', $post);

		$post->statusAccepted();
		$post->save();

		Post::flushCachedOnModerationCount();

		return $post;
	}
}
