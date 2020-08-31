<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Forum;
use App\Genre;
use App\Topic;
use Route;

class OldRoutesController extends Controller
{
	public function show()
	{
		switch (Route::current()->uri()) {
			case 'bs':

				$all = request()->all();

				$genres = array();

				if (isset($all['g'])) {
					preg_match('/sg([0-9]+)/iu', $all['g'], $matches);

					if (isset($matches['1'])) {
						$genres[] = $matches['1'];
					} else {
						preg_match('/g([0-9]+)/iu', $all['g'], $matches);

						if (isset($matches['1'])) {
							$genres = Genre::where('old_genre_group_id', $matches['1'])
								->notMain()
								->orderBy('id')
								->get()
								->pluck('id')
								->toArray();
						}
					}
				}

				unset($all['g']);

				if (count($genres) > 0) {
					$all['genre'] = $genres;
				}

				if (isset($all['p'])) {
					$all['page'] = $all['p'];
					unset($all['p']);
				}

				return redirect()->route('books', $all);
				break;
			case 'br':
				return redirect()->route('books.old.page', ['book' => (int)request()->input('b'), 'page' => request()->input('p')]);
				break;
			case 'bd':
				return redirect()->route('books.show',
					[
						'book' => (int)request()->input('b'),
						'page' => request()->input('p'),
						'comment' => request()->input('CommentId')
					]);
				break;
			case 'a':
				return redirect()->route('authors.show', ['author' => (int)request()->input('id')]);
				break;
			case 'series':
				return redirect()->route('sequences', request()->all());
				break;
			case 'books_in_series':
				return redirect()->route('sequences.show', ['sequence' => (int)request()->input('id')]);
				break;
			case 'Users':
				return redirect()->route('users', request()->all());
				break;
			case 'p':
				return redirect()->route('profile', [
					'user' => (int)request()->input('u'),
					'page' => request()->input('p'),
					'blog' => request()->input('GoToBlogRecord')
				]);
				break;
			case 'ForumRedirectToPost':
				return redirect()->route('posts.go_to', ['post' => (int)request()->input('PostId')]);
				break;
			case 'Topic':
				$id = pg_intval((int)request()->input('Id'));
				$topic = Topic::any()->findOrFail($id);

				if (empty($topic))
					abort(404);

				return redirect()->route('topics.show', [
					'topic' => $topic,
					'page' => request()->input('p'),
					'post' => request()->input('GoToPostId')
				]);

				break;
			case 'BookAddV2':
				return redirect()->route('books.create');
				break;
			case 'UserBookRate':
				return redirect()->route('users.votes', ['user' => (int)request()->input('UserId')]);
				break;
			case 'edit_profile':
				if (auth()->check())
					return redirect()->route('users.edit', ['user' => auth()->id()]);
				else
					return redirect()->route('home.latest_books');
				break;
			case 'UserComments':
				return redirect()->route('users.books.comments', ['user' => (int)request()->input('UserId')]);
				break;
			case 'UserLibBook':
				return redirect()->route('users.books', ['user' => (int)request()->input('UserId')]);
				break;
			case 'UserLibAuthor':
				return redirect()->route('users.authors', ['user' => (int)request()->input('UserId')]);
				break;
			case 'Forum':

				$id = pg_intval((int)request()->input('Id'));

				$forum = Forum::find($id);

				if (empty($forum))
					return redirect()->route('forums.index');

				return redirect()->route('forums.show', ['forum' => $forum->id, 'page' => request()->input('p')]);
				break;
			case 'as':
				return redirect()->route('authors', request()->all());
				break;
			case 'all_genre':
				return redirect()->route('genres');
				break;
			case 'BookRateShow':
				return redirect()->route('users.votes', ['user' => (int)request()->input('UserId')]);
				break;
			case 'ShowTalk':
				return redirect()->route('users.messages.index', ['user' => (int)request()->input('UserId')]);
				break;

			case 'UserGenreBlackList':
				if (auth()->check())
					return redirect()->route('genre_blacklist', ['user' => auth()->id()]);
				else
					return redirect()->route('home.latest_books');
				break;
			case 'MessageInbox':
				if (auth()->check())
					return redirect()->route('users.inbox', ['user' => auth()->id()]);
				else
					return redirect()->route('books');
				break;


			case 'main_page':
				return redirect()->route('home');
				break;
			case 'UserBookWithStatus':

				switch (request()->input('UserStatus')) {
					case '2':
						return redirect()->route('users.books.readed', ['user' => (int)request()->input('UserId')]);
						break;

					case '3':
						return redirect()->route('users.books.read_later', ['user' => (int)request()->input('UserId')]);
						break;

					case '4':
						return redirect()->route('users.books.read_now', ['user' => (int)request()->input('UserId')]);
						break;
				}

				break;

			case 'AuthorPageTabLoad':
				return redirect()->route('authors.show', ['author' => (int)request()->input('AuthorId')]);
				break;

			case 'UserBooks':
				return redirect()->route('users.books.created', ['user' => (int)request()->input('UserId')]);
				break;

			case 'UserPosts':
				return redirect()->route('users.posts', ['user' => (int)request()->input('UserId')]);
				break;

			case 'UserLibSequence':
				return redirect()->route('users.sequences', ['user' => (int)request()->input('UserId')]);
				break;

			case 'AllKeywords':
				return redirect()->route('keywords.index');
				break;

			case 'ForumPostSearch':

				if (request()->input('TopicId')) {
					return redirect()->route('topics.posts.index', ['topic' => (int)request()->input('TopicId'), 'page' => request()->input('p')]);
				} else {
					return redirect()->route('home.latest_posts', ['page' => request()->input('p')]);
				}

				break;
			case 'ShowUsersVoteForBook':
				return redirect()->route('books.votes', ['book' => (int)request()->input('BookId')]);
				break;

			case 'most_viewed_books':

				switch (request()->input('period')) {
					case 'today':
						return redirect()->route('home.popular_books', ['period' => 'day', 'page' => request()->input('p')]);
						break;
					case 'week':
						return redirect()->route('home.popular_books', ['period' => 'week', 'page' => request()->input('p')]);
						break;
					case 'month':
						return redirect()->route('home.popular_books', ['period' => 'month', 'page' => request()->input('p')]);
						break;
					case 'year':
						return redirect()->route('home.popular_books', ['period' => 'year', 'page' => request()->input('p')]);
						break;
					case 'all':
						return redirect()->route('books', ['order' => 'rating_year_desc', 'page' => request()->input('p')]);
						break;
					default:
						return redirect()->route('home.popular_books', ['period' => 'day', 'page' => request()->input('p')]);
						break;
				}

				break;

			case 'add_book_fb2':
				return redirect()->route('books.create');
				break;

			case 'Forums':
				return redirect()->route('forums.index');
				break;

			case 'BookRatingPeriod':

				switch (request()->input('period')) {
					case 'today':
						return redirect()->route('home.popular_books', ['period' => 'day', 'page' => request()->input('p')]);
						break;

					case 'week':
						return redirect()->route('home.popular_books', ['period' => 'week', 'page' => request()->input('p')]);
						break;

					case 'month':
						return redirect()->route('home.popular_books', ['period' => 'month', 'page' => request()->input('p')]);
						break;
					case 'year':
						return redirect()->route('home.popular_books', ['period' => 'year', 'page' => request()->input('p')]);
						break;
					case 'all':
						return redirect()->route('books', ['page' => request()->input('p')]);
						break;
					default:
						return redirect()->route('books', ['page' => request()->input('p')]);
						break;
				}

				break;

			case 'UsersOnModerate':
				return redirect()->route('users.on_moderation');
				break;

			case 'show_users':
				return redirect()->route('users', request()->all());
				break;

			case 'AuthorComments':
				return redirect()->route('authors.comments', ['author' => (int)request()->input('AuthorId')]);
				break;

			case 'AuthorRateShow':
				return redirect()->route('authors.books_votes', ['author' => (int)request()->input('AuthorId')]);
				break;
			case 'Comments':
				return redirect()->route('home.latest_comments', ['page' => request()->input('p')]);
				break;
			case 'BlogRecordRedirectTo':
				$blog = Blog::any()->findOrFail((int)request()->input('Id'));

				if (empty($blog) or empty($blog->owner))
					abort(404);

				return redirect()->route('users.blogs.go', ['user' => $blog->owner->id, 'blog' => $blog->id]);
				break;
			case 'CommentRedirectTo':
				return redirect()->route('comments.go', ['comment' => (int)request()->input('CommentId')]);
				break;
			case 'Rules':
				return redirect()->route('rules');
				break;
			case 'ForRightsOwners':
				return redirect()->route('for_rights_owners');
				break;
			case 'WhoLike':

				$id = intval(request()->input('Id'));

				switch (request()->input('Type')) {
					case 'ForumPost':
						$type = 'post';
						break;
					case 'BlogRecord':
						$type = 'blog';
						break;
					case 'Author':
						$type = 'author';
						break;
					case 'Book':
						$type = 'book';
						break;
					case 'Sequence':
						$type = 'sequence';
						break;
				}

				if (empty($type) or empty($id))
					abort(404);

				return redirect()->route('likes.users', ['type' => $type, 'id' => $id]);

				break;

			case 'AllBooks':
				return redirect()->route('books', request()->all());
				break;
		}
	}
}
