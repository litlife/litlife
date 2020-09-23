<?php

namespace App\Http\Controllers;

use App\Enums\TopicLabelEnum;
use App\Enums\VariablesEnum;
use App\Events\ForumCountOfPostsHasChanged;
use App\Events\TopicCountOfPostsHasChanged;
use App\Events\TopicViewed;
use App\Forum;
use App\Http\Requests\StorePost;
use App\Http\Requests\StoreQuestion;
use App\Http\Requests\StoreTopic;
use App\Jobs\Forum\UpdateForumCounters;
use App\Jobs\Topic\UpdateTopicCounters;
use App\Post;
use App\Topic;
use App\UserTopicSubscription;
use App\Variable;
use Artesaos\SEOTools\Facades\SEOMeta;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TopicController extends Controller
{
	/**
	 * Редирект на список тем в форуме
	 *
	 * @param Request $request
	 * @param Forum $forum
	 * @return View
	 */
	public function index(Request $request, Forum $forum)
	{
		return redirect()->route('forums.show', $forum);
	}

	/**
	 * Форма создания
	 * @param Forum $forum
	 * @return View
	 * @throws
	 */
	public function create(Forum $forum)
	{
		$this->authorize('create', Topic::class);

		return view('forum.topic.create', compact('forum'));
	}

	/**
	 * Сохранение
	 *
	 * @param StoreTopic $request
	 * @param Forum $forum
	 * @return Response
	 * @throws
	 */
	public function store(StoreTopic $request, Forum $forum)
	{
		$topic = new Topic();

		if (auth()->user()->cant('edit_spectial_settings', $topic)) {
			$fillable = collect($topic->getFillable())->filter(function ($value, $key) {
				if (!in_array($value, ['post_desc', 'main_priority', 'forum_priority', 'hide_from_main_page']))
					return $value;
			});

			$topic->fillable($fillable->toArray());
		}

		if (auth()->user()->created_topics()->where('name', $request->name)->where('created_at', '>', now()->subMinutes(5))->count() > 0) {
			return back()
				->withErrors(['name' => __('topic.you_have_recently_created_a_theme_with_the_same_name')]);
		}

		$topic->fill($request->all());

		$this->authorize('create', Topic::class);

		$this->validate($request, (new StorePost)->rules(), [], __('post'));

		$topic = $forum->topics()
			->save($topic);

		$post = new Post($request->all());
		$topic->posts()
			->save($post);

		if ($forum->isAutofixFirstPostInCreatedTopics() or $forum->isIdeaForum())
			$post->fix();

		if ($forum->isIdeaForum()) {
			$topic->label = TopicLabelEnum::IdeaOnReview;
			$topic->save();
		}

		return redirect()
			->route('topics.show', compact('topic'));
	}

	/**
	 * Отображение темы
	 *
	 * @param Topic $topic
	 * @return View
	 * @throws
	 */
	public function show(Topic $topic)
	{
		if ($topic->trashed())
			abort(404);

		$forum = $topic->forum;

		if (empty($forum) or $forum->trashed())
			abort(404);

		$this->authorize('view', $forum);

		js_put('topic_id', $topic->id);

		if ($topic->top_post_id)
			$top_post = $topic->top_post;
		else
			$top_post = null;

		$items = $topic->posts()
			->with(['create_user.latest_user_achievements.achievement.image', "edit_user", "topic", "create_user.groups"])
			->whereNull("tree")
			->when($top_post, function ($query) use ($top_post) {
				return $query->where('id', '!=', $top_post->id);
			})
			->when($topic->post_desc, function ($query) use ($topic) {
				return $query->latest();
			}, function ($query) {
				return $query->oldest();
			})
			->paginate();

		$ids = $items->pluck('id')->toArray();

		$descendants = $topic->posts()
			->with(['create_user.latest_user_achievements.achievement.image', "edit_user", "topic", "create_user.groups"])
			->where(function ($query) {
				$query->where('level', '<', '3');

				if (request()->post) {
					$go_to_post = Post::findOrFail(intval(request()->post));

					if (empty($go_to_post->root))
						$query->orDescendants($go_to_post->id);
					else
						$query->orDescendants($go_to_post->root->id);
				}
			})
			->descendants($ids)
			->oldest()
			->get();

		$items->load(['likes' => function ($query) {
			$query->where('create_user_id', auth()->id());
		}]);

		$descendants->load(['likes' => function ($query) {
			$query->where('create_user_id', auth()->id());
		}]);

		if (!empty($top_post)) {
			$top_post->load(['likes' => function ($query) {
				$query->where('create_user_id', auth()->id());
			}]);
		}

		if ($postMostLiked = $items->sortByDesc('like_count')->first()) {
			SEOMeta::setDescription($postMostLiked->getShareDescription());
		}
		/*
				if (auth()->check())
					$auth_user_topic_subscription = $topic->subscribed_users()
						->where('user_id', auth()->id())
						->first();
		*/
		event(new TopicViewed($topic));

		if (request()->ajax()) {
			return view('forum.topic.show_ajax', get_defined_vars());
		} else {
			return view('forum.topic.show', get_defined_vars());
		}
	}

	/**
	 * Форма редактирования
	 *
	 * @param Topic $topic
	 * @return View
	 * @throws
	 */
	public function edit(Topic $topic)
	{
		$this->authorize('update', $topic);

		return view('forum.topic.edit', compact('topic'));
	}

	/**
	 * Сохранение
	 *
	 * @param StoreTopic $request
	 * @param Topic $topic
	 * @return Response
	 * @throws
	 */
	public function update(StoreTopic $request, Topic $topic)
	{
		$this->authorize('update', $topic);

		if (auth()->user()->cant('edit_spectial_settings', $topic)) {
			$fillable = collect($topic->getFillable())->filter(function ($value, $key) {
				if (!in_array($value, ['post_desc', 'main_priority', 'forum_priority', 'hide_from_main_page']))
					return $value;
			});

			$topic->fillable($fillable->toArray());
		}

		$topic->fill($request->all());
		$topic->user_edited_at = now();
		$topic->save();

		return back()->withInput();
	}

	/**
	 * Удаляет или восстанавливает тему
	 *
	 * @param  $id
	 * @return Topic $topic
	 * @throws
	 */
	public function destroy($id)
	{
		$topic = Topic::any()->findOrFail($id);

		if ($topic->trashed()) {
			$this->authorize('restore', $topic);

			$topic->restore();
		} else {
			$this->authorize('delete', $topic);

			$topic->delete();
		}

		return $topic;
	}

	/**
	 * Открывает тему для добавления сообщений
	 *
	 * @param Topic $topic
	 * @return Response
	 * @throws
	 */

	public function open(Topic $topic)
	{
		$this->authorize('open', $topic);

		$topic->open();
		$topic->save();

		return back();
	}

	/**
	 * Закрывае тему для добавления сообщений
	 *
	 * @param Topic $topic
	 * @return Response
	 * @throws
	 */

	public function close(Topic $topic)
	{
		$this->authorize('close', $topic);

		$topic->close();
		$topic->save();

		return back();
	}

	/**
	 * Архивировать тему
	 *
	 * @param Topic $topic
	 * @return Response
	 * @throws
	 */
	public function archive(Topic $topic)
	{
		$this->authorize('archive', $topic);

		$topic->archive();
		$topic->save();

		return back();
	}

	/**
	 * Разархивировать тему
	 *
	 * @param Topic $topic
	 * @return Response
	 * @throws
	 */
	public function unarchive(Topic $topic)
	{
		$this->authorize('unarchive', $topic);

		$topic->unarchive();
		$topic->save();

		return back();
	}

	/**
	 * Форма объединения тем
	 *
	 * @param Topic $topic
	 * @return View
	 * @throws
	 */

	public function mergeForm(Topic $topic)
	{
		$this->authorize('merge', $topic);

		return view('forum.topic.merge', compact('topic'));
	}

	/**
	 * Объединение тем
	 *
	 * @param Topic $topic
	 * @param Request $request
	 * @return Response
	 * @throws
	 */
	public function merge(Request $request, Topic $topic)
	{
		$this->authorize('merge', $topic);

		$this->validate($request, [
			'topics' => 'required|array|exists:topics,id'
		]);

		$main_topic = $topic;

		$topics = Topic::find($request->input('topics'));

		DB::transaction(function () use ($topics, $main_topic) {

			$topics->each(function ($topic) use ($main_topic) {

				// переносим сообщений
				Post::where('topic_id', $topic->id)
					->update([
						'topic_id' => $main_topic->id,
						'forum_id' => $main_topic->forum_id,
						'private' => (bool)$main_topic->forum->private,
					]);

				// удаляем топик из которого сообщения были перенесены
				$topic->top_post_id = null;
				$topic->delete();

				UpdateTopicCounters::dispatch($topic);
				UpdateForumCounters::dispatch($topic->forum);
			});

			UpdateTopicCounters::dispatch($main_topic);
			UpdateForumCounters::dispatch($main_topic->forum);
		});

		return redirect()
			->route('topics.show', ['topic' => $main_topic, 'forum' => $main_topic->forum]);
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

		$query = Topic::void();

		if (is_numeric($str)) {
			$query->where('id', $str);
		} else {
			$query->fulltextSearch($str);
		}

		$items = $query->limit(10)->get();

		return ['items' => $items];
	}

	/**
	 * Форма перемещения
	 *
	 * @param Topic $topic
	 * @return View
	 * @throws
	 */
	public function moveForm(Topic $topic)
	{
		$this->authorize('move', $topic);

		return view('forum.topic.move', compact('topic'));
	}

	/**
	 * Перемещение
	 *
	 * @param Request $request
	 * @param Topic $topic
	 * @return Response
	 * @throws
	 */
	public function move(Request $request, Topic $topic)
	{
		$this->authorize('move', $topic);

		$this->validate($request, [
			'forum' => 'required|numeric|exists:forums,id'
		]);

		$forum = Forum::findOrFail($request->input('forum'));

		DB::transaction(function () use ($forum, $topic) {

			$old_forum = $topic->forum;

			// переносим сообщения
			Post::where('topic_id', $topic->id)
				->update([
					'forum_id' => $forum->id,
					'private' => (bool)$forum->private,
				]);

			$topic->forum_id = $forum->id;
			$topic->save();

			// обновляем форум откуда была перенесена тема
			UpdateForumCounters::dispatch($old_forum);
			// обновляем форум куда была перенесена тема
			UpdateForumCounters::dispatch($forum);
		});

		return back()
			->with('success', __('topic.moved', ['url' => route('forums.show', $forum), 'name' => $forum->name]));
	}

	public function changeLabel(Topic $topic, $label)
	{
		$this->authorize('update', $topic);
		$this->authorize('edit_spectial_settings', $topic);

		$topic->label = $label;
		$topic->save();

		return back();
	}

	public function subscribeToggle(Request $request, Topic $topic)
	{
		$subscription = UserTopicSubscription::where('topic_id', $topic->id)
			->where('user_id', auth()->id())
			->first();

		if (empty($subscription)) {
			$this->authorize('subscribe', $topic);

			$user_relation = UserTopicSubscription::updateOrCreate(
				['user_id' => auth()->id(), 'topic_id' => $topic->id],
				[]
			);

			if ($request->ajax())
				return ['status' => 'subscribed'];
			else {
				return view('success', ['success' => __('topic.you_have_successfully_subscribed_to_receive_notifications_of_new_messages')]);
			}
		} else {
			$this->authorize('unsubscribe', $topic);

			UserTopicSubscription::where('topic_id', $topic->id)
				->where('user_id', auth()->id())
				->delete();

			if ($request->ajax())
				return ['status' => 'unsubscribed'];
			else {
				return view('success', ['success' => __('topic.you_have_successfully_unsubscribed_from_receiving_notifications_of_new_messages')]);
			}
		}

		/*
		if ($topic->auth_user_subscription)
		{
			$this->authorize('unsubscribe', $topic);

			UserTopicSubscription::where('topic_id', $topic->id)
				->where('user_id', auth()->id())
				->delete();

			if ($request->ajax())
				return ['result' => 'unsubscribed'];
		}
		else
		{
			$this->authorize('subscribe', $topic);

			$user_relation = UserTopicSubscription::updateOrCreate(
				['user_id' => auth()->id(), 'topic_id' => $topic->id],
				[]
			);

			if ($request->ajax())
				return ['result' => 'subscribed'];
		}

		return redirect()->back();
		*/
	}

	public function unsubscribe(Topic $topic)
	{
		$this->authorize('unsubscribe', $topic);

		UserTopicSubscription::where('topic_id', $topic->id)
			->where('user_id', auth()->id())
			->delete();

		return redirect()->back();
	}

	/**
	 * Архив тем
	 *
	 * @return View
	 * @throws
	 */
	public function archived()
	{
		$topics = Topic::archived()
			->orderByLastPostDescNullsLast()
			->simplePaginate();

		return view('forum.topic.archived', compact('topics'));
	}

	/**
	 * Сохранение
	 *
	 * @param StoreQuestion $request
	 * @return Response
	 * @throws
	 */
	public function storeQuestion(StoreQuestion $request)
	{
		$id = Variable::where('name', VariablesEnum::ForumOfQuestions)
			->firstOrFail()
			->value;

		$forum = Forum::findOrFail($id);

		$this->authorize('create_topic', $forum);

		$topic = new Topic();

		if (auth()->user()->created_topics()->where('name', $request->name)->where('created_at', '>', now()->subMinutes(5))->count() > 0) {
			return back()
				->withErrors(['name' => __('topic.you_have_recently_created_a_theme_with_the_same_name')]);
		}

		$topic->fill($request->all());

		$topic = $forum
			->topics()
			->save($topic);

		$post = new Post($request->all());
		$topic->posts()->save($post);

		$post->fix();

		if ($request->notify_about_responses) {
			$topic->user_subscriptions()
				->updateOrCreate(['user_id' => auth()->id()], []);
		}

		return redirect()
			->route('topics.show', compact('topic'));
	}
}
