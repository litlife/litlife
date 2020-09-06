<?php

namespace App\Console\Commands;

use App\AuthorStatus;
use App\Book;
use App\Bookmark;
use App\BookmarkFolder;
use App\BookStatus;
use App\BookVote;
use App\Comment;
use App\CommentVote;
use App\Image;
use App\Keyword;
use App\Like;
use App\Manager;
use App\Message;
use App\Post;
use App\Sequence;
use App\Topic;
use App\User;
use App\UserAuthor;
use App\UserBook;
use App\UserEmail;
use App\UserGenreBlacklist;
use App\UserOnModeration;
use App\UserPhoto;
use App\UserRelation;
use App\UsersAccessToForum;
use App\UserSequence;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UsersMerge extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'users:merge {main_id} {ids*}';
	private $main_user;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->main_user = User::any()->findOrFail($this->argument('main_id'));

		if (empty($this->main_user->id))
			throw new Exception('Id empty');

		$ids = $this->argument('ids');

		$users = User::any()
			->whereIn('id', $ids)
			->orderBy('last_activity_at', 'desc')
			->get();

		if (count($users) < 1) {
			$this->error('Найдено менее 1 авторов');
			return false;
		}

		DB::transaction(function () use ($users) {

			foreach ($users as $user) {
				$this->item($user);

				$user->suspend();
				$user->save();
			}
		});
	}

	private function item($user)
	{
		echo($user->last_activity_at . "\n");

		$this->book_statuses($user);
		$this->book_votes($user);
		$this->bookmarks($user);
		$this->books($user);
		$this->comment_votes($user);
		$this->comments($user);
		$this->images($user);
		$this->keywords($user);
		$this->likes($user);
		$this->managers($user);
		$this->messages($user);
		$this->posts($user);
		$this->sequences($user);
		$this->topics($user);
		$this->user_authors($user);
		$this->user_books($user);
		$this->user_sequences($user);
		$this->user_emails($user);
		$this->user_genre_blacklist($user);
		$this->user_photos($user);
		$this->user_relation($user);
		$this->access_to_forums($user);
		$this->users_on_moderation($user);
	}

	private function book_statuses($user)
	{
		$main_user_book_statuses = BookStatus::where('user_id', $this->main_user->id)->get();

		$book_statuses = BookStatus::where('user_id', $user->id)->get();

		foreach ($book_statuses as $book_status) {
			if (!$main_user_book_statuses->where('book_id', $book_status->book_id)->first()) {
				$book_status->user_id = $this->main_user->id;
				$book_status->save();
			}
		}
	}

	private function book_votes($user)
	{
		$main_user_votes = BookVote::where('create_user_id', $this->main_user->id)->get();

		$votes = BookVote::where('create_user_id', $user->id)->get();

		foreach ($votes as $vote) {
			if (!$main_user_votes->where('book_id', $vote->book_id)->first()) {
				$vote->create_user_id = $this->main_user->id;
				$vote->save();
			}
		}
	}

	private function bookmarks($user)
	{
		$bookmarks = Bookmark::where('create_user_id', $user->id)->get();

		foreach ($bookmarks as $bookmark) {
			$bookmark->create_user_id = $this->main_user->id;
			$bookmark->save();
		}

		$bookmark_folders = BookmarkFolder::where('create_user_id', $user->id)->get();

		foreach ($bookmark_folders as $bookmark_folder) {
			$bookmark_folder->create_user_id = $this->main_user->id;
			$bookmark_folder->save();
		}
	}

	private function books($user)
	{
		$books = Book::where('create_user_id', $user->id)->get();

		foreach ($books as $book) {
			$book->create_user_id = $this->main_user->id;
			$book->save();
		}
	}

	private function comment_votes($user)
	{
		$votes = CommentVote::where('create_user_id', $user->id)->get();

		$main_user_votes = CommentVote::where('create_user_id', $this->main_user->id)->get();

		foreach ($votes as $vote) {
			if (!$main_user_votes->where('comment_id', $vote->comment_id)->first()) {
				$vote->create_user_id = $this->main_user->id;
				$vote->save();
			}
		}
	}

	private function comments($user)
	{
		$comments = Comment::where('create_user_id', $user->id)->get();

		foreach ($comments as $comment) {
			$comment->create_user_id = $this->main_user->id;
			$comment->save();
		}
	}

	private function images($user)
	{
		$items = Image::where('create_user_id', $user->id)->get();

		foreach ($items as $item) {
			$item->create_user_id = $this->main_user->id;
			$item->save();
		}
	}

	private function keywords($user)
	{
		$items = Keyword::where('create_user_id', $user->id)->get();

		foreach ($items as $item) {
			$item->create_user_id = $this->main_user->id;
			$item->save();
		}
	}

	private function likes($user)
	{
		$main_items = Like::where('create_user_id', $this->main_user->id)->get();

		$items = Like::where('create_user_id', $user->id)->get();

		foreach ($items as $item) {
			if (!$main_items->where('likeable_type', $item->likeable_type)
				->where('likeable_id', $item->likeable_id)
				->first()) {
				$item->create_user_id = $this->main_user->id;
				$item->save();
			}
		}
	}

	private function managers($user)
	{
		$main_items = Manager::where('create_user_id', $this->main_user->id)->get();

		$items = Manager::where('create_user_id', $user->id)->get();

		foreach ($items as $item) {
			$item->create_user_id = $this->main_user->id;
			$item->save();
		}

		$main_items = Manager::where('user_id', $this->main_user->id)->get();

		$items = Manager::where('user_id', $user->id)->get();

		foreach ($items as $item) {
			if (!$main_items->where('manageable_type', $item->manageable_type)
				->where('manageable_id', $item->manageable_id)
				->first()) {
				$item->user_id = $this->main_user->id;
				$item->save();
			}
		}
	}

	private function messages($user)
	{
		$items = Message::where('recepient_id', $user->id)->get();

		foreach ($items as $item) {
			$item->recepient_id = $this->main_user->id;
			$item->save();
		}

		$items = Message::where('sender_id', $user->id)->get();

		foreach ($items as $item) {
			$item->sender_id = $this->main_user->id;
			$item->save();
		}
	}

	private function posts($user)
	{
		$items = Post::where('create_user_id', $user->id)->get();

		foreach ($items as $item) {
			$item->create_user_id = $this->main_user->id;
			$item->save();
		}

		$items = Post::where('edit_user_id', $user->id)->get();

		foreach ($items as $item) {
			$item->edit_user_id = $this->main_user->id;
			$item->save();
		}
	}

	private function sequences($user)
	{
		$items = Sequence::where('create_user_id', $user->id)->get();

		foreach ($items as $item) {
			$item->create_user_id = $this->main_user->id;
			$item->save();
		}
	}

	private function topics($user)
	{
		$items = Topic::where('create_user_id', $user->id)->get();

		foreach ($items as $item) {
			$item->create_user_id = $this->main_user->id;
			$item->save();
		}
	}

	private function user_authors($user)
	{
		$main_items = UserAuthor::where('user_id', $this->main_user->id)->get();

		$items = UserAuthor::where('user_id', $user->id)->get();

		foreach ($items as $item) {
			if (!$main_items->where('author_id', $item->author_id)
				->first()) {
				$item->user_id = $this->main_user->id;
				$item->save();
			}
		}
	}

	private function user_books($user)
	{
		$main_items = UserBook::where('user_id', $this->main_user->id)->get();

		$items = UserBook::where('user_id', $user->id)->get();

		foreach ($items as $item) {
			if (!$main_items->where('book_id', $item->book_id)
				->first()) {
				$item->user_id = $this->main_user->id;
				$item->save();
			}
		}
	}

	private function user_sequences($user)
	{
		$main_items = UserSequence::where('user_id', $this->main_user->id)->get();

		$items = UserSequence::where('user_id', $user->id)->get();

		foreach ($items as $item) {
			if (!$main_items->where('sequence_id', $item->sequence_id)
				->first()) {
				$item->user_id = $this->main_user->id;
				$item->save();
			}
		}
	}

	private function user_emails($user)
	{
		$items = UserEmail::where('user_id', $user->id)->get();

		foreach ($items as $item) {
			if (!UserEmail::where('user_id', $this->main_user->id)->where('email', 'ilike', '%' . $item->email . '%')
				->first()) {
				$item->user_id = $this->main_user->id;
				$item->save();
			} else {
				$item->delete();
			}
		}
	}

	private function user_genre_blacklist($user)
	{
		$main_items = UserGenreBlacklist::where('user_id', $this->main_user->id)->get();

		$items = UserGenreBlacklist::where('user_id', $user->id)->get();

		foreach ($items as $item) {
			if (!$main_items->where('user_id', $item->user_id)
				->first()) {
				$item->user_id = $this->main_user->id;
				$item->save();
			}
		}
	}

	private function user_photos($user)
	{
		$items = UserPhoto::where('user_id', $user->id)->get();

		foreach ($items as $item) {
			$item->user_id = $this->main_user->id;
			$item->save();
		}
	}

	private function user_relation($user)
	{
		$items = UserRelation::where('user_id', $user->id)->get();

		foreach ($items as $item) {
			if (!UserRelation::where('user_id', $this->main_user->id)
				->where('user_id2', $item->user_id2)
				->first()) {
				$item->user_id = $this->main_user->id;
				$item->save();
			}
		}

		$items = UserRelation::where('user_id2', $user->id)->get();

		foreach ($items as $item) {
			if (!UserRelation::where('user_id2', $this->main_user->id)
				->where('user_id', $item->user_id)
				->first()) {
				$item->user_id2 = $this->main_user->id;
				$item->save();
			}
		}
	}

	private function access_to_forums($user)
	{
		$main_items = UsersAccessToForum::where('user_id', $this->main_user->id)->get();

		$items = UsersAccessToForum::where('user_id', $user->id)->get();

		foreach ($items as $item) {
			if (!$main_items->where('user_id', $item->user_id)
				->first()) {
				$item->user_id = $this->main_user->id;
				$item->save();
			}
		}
	}

	private function users_on_moderation($user)
	{
		$main_items = UserOnModeration::where('user_id', $this->main_user->id)->get();

		$items = UserOnModeration::where('user_id', $user->id)->get();

		foreach ($items as $item) {
			if (!$main_items->where('user_id', $item->user_id)
				->first()) {
				$item->user_id = $this->main_user->id;
				$item->save();
			}
		}
	}

	private function author_statuses($user)
	{
		$main_user_items = AuthorStatus::where('user_id', $this->main_user->id)->get();

		$items = AuthorStatus::where('user_id', $user->id)->get();

		foreach ($items as $item) {
			if (!$main_user_items->where('author_id', $item->author_id)->first()) {
				$item->user_id = $this->main_user->id;
				$item->save();
			}
		}
	}
}
