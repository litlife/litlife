<?php

namespace App\Http\Controllers;

use App\Book;
use App\Comment;
use App\CommentVote;
use App\Http\Requests\StoreComment;
use App\Jobs\Mail\NewCommentReplyNotificationJob;
use App\Notifications\CommentVoteNotification;
use Auth;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CommentController extends Controller
{
	/**
	 * Список комментариев книги
	 *
	 * @param Book $book
	 * @return View
	 */
	public function index()
	{

		return redirect()
			->route('home.latest_comments');
		/*
		$top_comments = $book->topComments();

		$comments = $book->otherComments()->simplePaginate();

		return view("comment/index", [
			'book' => $book,
			'top_comments' => $top_comments,
			'comments' => $comments
		]);
		*/
	}

	/**
	 * Форма добавления комментария
	 * @param string $commentable_type
	 * @param number $commentable_id
	 * @param Request $request
	 * @return View
	 * @throws
	 */
	public function create($commentable_type, $commentable_id, Request $request)
	{
		$model = Relation::getMorphedModel($commentable_type);

		if (empty($model))
			abort(404);

		$commentable = $model::findOrFail($commentable_id);

		if ($request->parent) {
			$parent = Comment::findOrFail($request->parent);

			$this->authorize('reply', $parent);
		}

		$this->authorize('commentOn', $commentable);

		$data = [
			'commentable_type' => $commentable_type,
			'commentable_id' => $commentable_id,
			'parent' => $parent ?? null,
			'level' => isset($parent->level) ? $parent->level + 1 : 0
		];

		if (request()->ajax())
			return view('comment.create_form', $data);
		else
			return view('comment.create', $data);
	}

	/**
	 * Сохранение
	 *
	 * @param StoreComment $request
	 * @param string $commentable_type
	 * @param integer $commentable_id
	 * @return Response
	 * @throws
	 */
	public function store(StoreComment $request, $commentable_type, $commentable_id)
	{
		$model = Relation::getMorphedModel($commentable_type);

		if (empty($model))
			abort(404);

		$commentable = $model::findOrFail($commentable_id);

		if (method_exists($commentable, 'isInGroup') and $commentable->isInGroup() and $commentable->isNotMainInGroup())
			$mainCommentable = $commentable;
		else
			$mainCommentable = $commentable;

		if ($request->parent) {
			$parent = Comment::findOrFail($request->parent);

			$this->authorize('reply', $parent);
		}

		$this->authorize('commentOn', $mainCommentable);

		if (auth()->user()->comments()->where('created_at', '>', now()->subMinutes(10))->count() >= 10)
			return back()->withErrors(['bb_text' => __('comment.you_comment_to_fast')]);

		$comment = new Comment($request->all());

		if (!empty($parent))
			$comment->parent = $parent;

		if ($request->leave_for_personal_access) {
			if ($mainCommentable instanceof Book) {
				if (empty($parent)) {
					$comment->statusPrivate();
				}
			}
		}

		$comment = $mainCommentable
			->comments()
			->save($comment);

		if (request()->ajax())
			return $comment;
		else
			return redirect()->route('comments.go', compact('comment'));
	}

	/**
	 * Show one comment
	 * @param Comment $comment
	 * @return View
	 * @throws
	 */
	public function show(Comment $comment)
	{
		return view('comment.list.default', ['item' => $comment, 'parent' => $comment->parent ?? null, 'level' => $comment->level, 'no_book_link' => true]);
	}

	/**
	 * Форма редктирования
	 *
	 * @param Comment $comment
	 * @return View
	 * @throws
	 */
	public function edit(Comment $comment)
	{
		$this->authorize('update', $comment);

		if (request()->ajax())
			return view('comment.edit_form', compact('comment'));
		else
			return view("comment.edit", compact('comment'));
	}

	/**
	 * Сохранение отредатированного
	 *
	 * @param StoreComment $request
	 * @param Comment $comment
	 * @return Response
	 * @throws
	 */
	public function update(StoreComment $request, Comment $comment)
	{
		$this->authorize('update', $comment);

		DB::beginTransaction();

		$comment->fill($request->all());
		$comment->edit_user_id = auth()->id();
		$comment->user_edited_at = now();
		$comment->save();

		DB::commit();

		if (request()->ajax())
			return $comment;
		else
			return redirect()
				->route('comments.go', compact('comment'));
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
		$comment = Comment::any()->findOrFail($id);

		if ($comment->trashed()) {
			$this->authorize('restore', $comment);

			$comment->restore();
		} else {
			$this->authorize('delete', $comment);

			$comment->delete();
		}

		return $comment;
	}

	/**
	 * Переход к комментарию
	 *
	 * @param Comment $comment
	 * @return Response
	 */
	public function go_To(Comment $comment)
	{
		$root = $comment->root;

		if ($comment->isBookType()) {

			$commentable = $comment->originCommentable ?? abort(404);

			$count = $commentable->commentsOrigin()
				->roots()
				->when($root, function ($query) use ($root) {
					return $query->where('created_at', '>=', $root->created_at);
				}, function ($query) use ($comment) {
					return $query->where('created_at', '>=', $comment->created_at);
				})
				->orderByOriginFirstAndLatest($commentable)
				->count();

			$page = ceil($count / $comment->getPerPage());

			return redirect()
				->route('books.show', [
					'book' => $commentable,
					'page' => $page,
					'comment' => $comment,
					'#comment_' . $comment->id
				]);
		} elseif ($comment->isCollectionType()) {

			$commentable = $comment->commentable ?? abort(404);

			$count = $commentable->comments()
				->roots()
				->when($root, function ($query) use ($root) {
					return $query->where('created_at', '>=', $root->created_at);
				}, function ($query) use ($comment) {
					return $query->where('created_at', '>=', $comment->created_at);
				})
				->latest()
				->count();

			$page = ceil($count / $comment->getPerPage());

			return redirect()
				->route('collections.comments', [
					'collection' => $commentable,
					'page' => $page,
					'comment' => $comment,
					'#comment_' . $comment->id
				]);
		}
	}

	/**
	 * Вывод всех ответов комментария
	 *
	 * @param Comment $comment
	 * @return View
	 */
	public function descendants(Comment $comment)
	{
		$descendants = $comment->commentable->comments()
			->with("create_user.avatar")
			->descendants($comment->id)
			->oldest()
			->get();

		$descendants->load(['votes' => function ($query) {
			$query->where("create_user_id", auth()->id());
		}]);

		$level = request()->level ?? null;
		$level++;

		return view('comment.descendants', compact('comment', 'descendants', 'level'));
	}

	/**
	 * Голосование за комментарий
	 *
	 * @param Comment $comment
	 * @param int $vote
	 * @return array
	 * @throws
	 */
	public function vote(Comment $comment, $vote)
	{
		$this->authorize('vote', $comment);

		$comment_vote = $comment->votes()
			->where('create_user_id', Auth::id())
			->first();

		DB::transaction(function () use ($comment, $comment_vote, $vote) {

			if (!empty($comment->create_user->id) and $comment->create_user->id == auth()->id()) {

				if (!empty($comment_vote->vote)) {
					if ($comment_vote->vote != 0) {
						$comment_vote->vote = 0;
						$comment_vote->save();
					}
				} else {
					$comment->updateVotes();
				}

			} else {
				if (empty($comment_vote)) {
					$comment_vote = new CommentVote;
					$comment_vote->vote = $vote;
				} else {
					if ((($comment_vote->vote > 0) and ($vote > 0)) or (($comment_vote->vote < 0) and ($vote < 0))) {
						$comment_vote->vote = 0;
					} else {
						$comment_vote->vote = $vote;
					}
				}

				$comment->votes()->save($comment_vote);

				if ($comment_vote->vote > 0) {
					if (!empty($comment->create_user))
						$comment->create_user->notify(new CommentVoteNotification($comment_vote));
				}
			}
		});

		$comment->refresh();

		return [
			'vote' => $comment->vote,
			'vote_up' => $comment->vote_up,
			'vote_down' => 0
		];
	}

	/**
	 * На проверке
	 *
	 * @return View
	 */
	public function onCheck()
	{
		$comments = Comment::sentOnReview()->get();

		return view('comment.on_check', compact('comments'));
	}

	/**
	 * Одобрить
	 *
	 * @param Comment $comment
	 * @return Comment $comment
	 * @throws
	 */
	public function approve($comment)
	{
		$comment = Comment::any()->findOrFail($comment);

		$this->authorize('approve', $comment);

		$comment->statusAccepted();
		$comment->save();

		Comment::flushCachedOnModerationCount();

		return $comment;
	}

	/**
	 * Опубликовать комментарий
	 *
	 * @param Comment $comment
	 * @return Comment $comment
	 * @throws
	 */
	public function publish(Comment $comment)
	{
		$this->authorize('publish', $comment);

		$comment->statusAccepted();
		$comment->save();

		if ($comment->commentable instanceof Book) {
			$comment->commentable->refreshCommentCount();
			$comment->commentable->save();
		}

		Comment::flushCachedOnModerationCount();

		return $comment;
	}
}