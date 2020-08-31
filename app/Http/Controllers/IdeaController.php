<?php

namespace App\Http\Controllers;

use App\Enums\TopicLabelEnum;
use App\Enums\VariablesEnum;
use App\Forum;
use App\Http\Requests\StoreIdea;
use App\Post;
use App\Topic;
use App\User;
use App\Variable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IdeaController extends Controller
{
	/**
	 * Вывод главной страницы
	 *
	 * @return View
	 */
	public function index(Request $request)
	{
		$value = Variable::where('name', VariablesEnum::IdeaForum)->firstOrFail()->value;

		$forum = Forum::findOrFail($value);

		$topics = $forum->topics()
			->with('top_post.authUserLike')
			->addSelect('topics.*', 'posts.like_count')
			->leftJoin('posts', 'topics.top_post_id', '=', 'posts.id')
			->orderForIdeaForum()
			->simplePaginate(30);

		return view('ideas.index', ['items' => $topics]);
	}

	public function store(StoreIdea $request)
	{
		$value = Variable::where('name', VariablesEnum::IdeaForum)->firstOrFail()->value;

		$forum = Forum::findOrFail($value);

		if (auth()->user()->created_topics()->where('name', $request->name)->where('created_at', '>', now()->subMinutes(5))->count() > 0) {
			return back()
				->withErrors(['name' => __('idea.you_have_recently_created_a_idea_with_the_same_name')], 'idea')
				->withInput($request->all());
		}

		$topic = new Topic();

		$this->authorize('createAnIdea', User::class);

		$topic->fill($request->all());
		$topic->label = TopicLabelEnum::IdeaOnReview;
		$topic = $forum->topics()->save($topic);

		$post = new Post($request->all());
		$topic->posts()->save($post);
		$post->fix();

		if ($request->enable_notifications_of_new_messages) {
			$topic->user_subscriptions()
				->updateOrCreate(['user_id' => auth()->id()], []);
		}

		return redirect()
			->route('topics.show', compact('topic'));
	}

	public function hideCard(Request $request)
	{
		$request->session()->put('dont_show_idea_card', true);

		return ['result' => 'idea_card_is_hidden'];
	}

	public function search(Request $request)
	{
		$value = Variable::where('name', VariablesEnum::IdeaForum)->firstOrFail()->value;

		$forum = Forum::findOrFail($value);

		$topics = $forum->topics()
			->trgmSearch($request->name)
			->with('top_post.authUserLike')
			->simplePaginate();

		return view('ideas.search', ['items' => $topics]);
	}
}
