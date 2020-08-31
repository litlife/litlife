<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Enums\UserRelationType;
use App\Http\Requests\StoreBlog;
use App\Http\SearchResource\BlogPostSearchResource;
use App\Jobs\Mail\NewWallMessageNotificationJob;
use App\Jobs\Mail\NewWallReplyNotificationJob;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BlogController extends Controller
{
	/**
	 * Последние сообщения в блогах
	 *
	 * @return View
	 * @throws
	 */
	public function news()
	{
		$user = auth()->user();

		$blogs = Blog::whereHas('owner.relationshipReverse', function ($query) use ($user) {
			$query->whereIn('status', [UserRelationType::Subscriber, UserRelationType::Friend])
				->where('user_id', $user->id)
				->select('user_id2');
		})
			->where('create_user_id', '!=', $user->id)
			->latest()
			->with([
				"create_user.avatar",
				'create_user.groups',
				'create_user.account_permissions',
				'create_user.relationship',
				'owner.setting'
			])
			->simplePaginate();

		$blogs->loadMissing(["owner.avatar",
			'likes' => function ($query) use ($user) {
				$query->where('create_user_id', $user->id);
			}, 'owner.relationship' => function ($query) use ($user) {
				$query->where("user_id2", $user->id);
			}, "owner.account_permissions", "owner.groups"]);

		if (optional($blogs->max('created_at'))->timestamp > optional($user->data->last_news_view_at)->timestamp) {

			//if (!empty($blogs->where('created_at', '>', $user->data->last_news_view_at)->first())) {
			$user->data->last_news_view_at = now();
			$user->data->save();

			$user->flushNotViewedFriendsNewsCount();
		}

		if (request()->ajax())
			return view('blog.news_ajax', ['items' => $blogs])->render();

		return view('blog.news', ['items' => $blogs]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param User $user
	 * @return View
	 */
	public function index(User $user)
	{
		$blogs = $user->blog()
			->with("create_user.avatar", "owner.avatar")
			->roots()
			->orderBy("created_at", "desc")
			->simplePaginate();

		$blogs->load(['likes' => function ($query) {
			$query->where('create_user_id', auth()->id());
		}]);

		$descendants = $user->blog()
			->with("create_user.avatar", "owner.avatar")
			->descendants($blogs->pluck('id')->toArray())
			->orderBy("created_at", "desc")
			->get();

		$descendants->load(['likes' => function ($query) {
			$query->where('create_user_id', auth()->id());
		}]);

		return view("blog.index", [
			'user' => $user,
			'blogs' => $blogs,
			'descendants' => $descendants
		]);
	}

	/**
	 * Show one blog post
	 *
	 * @param Blog $blog
	 * @return View
	 * @throws
	 */
	public function show(Blog $blog)
	{
		return view('blog.list.default', ['item' => $blog, 'parent' => $blog->parent ?? null, 'level' => $blog->level]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Request $request
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function create(Request $request, User $user)
	{
		if (!empty($request->parent)) {

			$parent = Blog::findOrFail($request->parent);

			$this->authorize('reply', $parent);

		} else
			$this->authorize('writeOnWall', $user);

		$data = [
			'user' => $user,
			'parent' => $parent,
			'level' => isset($parent->level) ? $parent->level + 1 : 0
		];

		if (request()->ajax())
			return view('blog.create_form', $data);
		else
			return view('blog.create', $data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreBlog $request
	 * @param User $user
	 * @return mixed
	 * @throws
	 */
	public function store(StoreBlog $request, User $user)
	{
		if (!empty($request->parent)) {
			$parent = Blog::findOrFail($request->parent);

			$this->authorize('reply', $parent);
		} else {
			$this->authorize('writeOnWall', $user);
		}

		$blog = new Blog($request->all());

		if (!empty($parent))
			$blog->parent = $parent;

		$blog = $user->blog()->save($blog);

		if (request()->ajax())
			return $blog;
		else
			return redirect()->route('users.blogs.go', compact('user', 'blog'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param User $user
	 * @param Blog $blog
	 * @return View
	 * @throws
	 */
	public function edit(User $user, Blog $blog)
	{
		$this->authorize('update', $blog);

		if (request()->ajax())
			return view('blog.edit_form', compact('user', 'blog'));
		else
			return view('blog.edit', compact('user', 'blog'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreBlog $request
	 * @param User $user
	 * @param Blog $blog
	 * @return Response
	 * @throws
	 */
	public function update(StoreBlog $request, User $user, Blog $blog)
	{
		$this->authorize('update', $blog);

		DB::beginTransaction();

		$blog->fill($request->all());
		$blog->user_edited_at = now();
		$user->blog()->save($blog);

		DB::commit();

		if (request()->ajax())
			return $blog;
		else
			return redirect()
				->route('users.blogs.go', compact('user', 'blog'));

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param User $user
	 * @return int $blog_id
	 * @throws
	 */
	public function destroy(User $user, $blog_id)
	{
		$blog = Blog::withTrashed()->findOrFail($blog_id);

		if ($blog->trashed()) {
			$this->authorize('restore', $blog);
			$blog->restore();
		} else {
			$this->authorize('delete', $blog);
			$blog->delete();
		}

		return $blog;
	}

	/**
	 * Редирект к сообщению
	 *
	 * @param User $user
	 * @param Blog $blog
	 * @return Response
	 * @throws
	 */
	public function go_To(User $user, Blog $blog)
	{
		if ($blog->isFixed()) {
			return redirect()->route('profile', [
				'user' => $blog->owner,
				'blog' => $blog->id,
				'#blog_' . $blog->id
			]);
		}

		$root = $blog->root;

		$count = $user->blog()
			->roots()
			->when($root, function ($query) use ($root) {
				return $query->where('created_at', '>=', $root->created_at);
			}, function ($query) use ($blog) {
				return $query->where('created_at', '>=', $blog->created_at);
			})->count();

		$page = ceil($count / $blog->getPerPage());

		return redirect()->route('profile',
			[
				'user' => $blog->owner,
				'page' => $page,
				'blog' => $blog->id,
				'#blog_' . $blog->id
			]);
	}

	/**
	 * Вывод ответов на сообщение
	 *
	 * @param User $user
	 * @param Blog $blog
	 * @return View
	 * @throws
	 */
	public function descendants(User $user, Blog $blog)
	{
		$descendants = $user->blog()
			->with(["create_user.avatar", "owner.relationship", "owner.user_group", "create_user.relationship", "owner.account_permissions"])
			->descendants($blog->id)
			->orderBy("created_at", "asc")
			->get();

		$descendants->load(['likes' => function ($query) {
			$query->where('create_user_id', auth()->id());
		}]);


		$level = request()->level ?? null;
		$level++;

		return view('blog.childs', [
			'item' => $blog,
			'user' => $user,
			'descendants' => $descendants,
			'level' => $level
		]);
	}

	/**
	 * Закрепить сообщение
	 *
	 * @param User $user
	 * @param Blog $blog
	 * @return Response
	 * @throws
	 */
	public function fix(User $user, Blog $blog)
	{
		$this->authorize('fix', $blog);

		$user->setting->blog_top_record = $blog->id;
		$user->setting->save();

		return redirect()
			->route('profile', ['user' => $user, 'blog' => $blog, '#blog_' . $blog->id]);
	}

	/**
	 * Открепить сообщение
	 *
	 * @param User $user
	 * @param Blog $blog
	 * @return Response
	 * @throws
	 */
	public function unfix(User $user, Blog $blog)
	{
		$this->authorize('unfix', $blog);

		$user->setting->blog_top_record = null;
		$user->setting->save();

		return redirect()
			->route('users.blogs.go', compact('user', 'blog'));
	}

	/**
	 * Сообщения со стены на проверке
	 *
	 * @return Response
	 * @throws
	 */
	public function onReview()
	{
		$this->authorize('viewOnCheck', Blog::class);

		$builder = Blog::void()->onCheck();

		$resource = (new BlogPostSearchResource(request(), $builder))
			->defaultSorting('created_at_desc')
			->setSimplePaginate(true);

		$vars = $resource->getVars();

		$vars['blogs'] = $resource->getQuery()->simplePaginate();

		if (request()->ajax())
			return $resource->renderAjax($vars);

		return view('blog.on_check', $vars);
	}

	/**
	 * Сообщения со стены на проверке
	 *
	 * @param Blog $blog
	 * @return Blog $blog
	 * @throws
	 */
	public function approve(Blog $blog)
	{
		$this->authorize('approve', $blog);

		$blog->statusAccepted();
		$blog->save();

		Blog::flushCachedOnModerationCount();

		return $blog;
	}
}
