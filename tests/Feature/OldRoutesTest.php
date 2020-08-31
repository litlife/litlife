<?php

namespace Tests\Feature;

use App\Blog;
use App\Forum;
use App\Genre;
use App\Topic;
use App\User;
use Tests\TestCase;

class OldRoutesTest extends TestCase
{
	public function testUserGenreBlackList()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user)
			->get('/UserGenreBlackList')
			->assertRedirect(route('genre_blacklist', ['user' => $user]));
	}

	public function testBs()
	{
		$this->get('/bs')
			->assertRedirect(route('books'));
	}

	public function testBooksGenre()
	{
		$genre = factory(Genre::class)->create();

		$this->call('get', 'bs', ['g' => 'sg' . $genre->id])
			->assertRedirect(route('books', ['genre' => [$genre->id]]));
	}

	public function testBooksGenreEmpty()
	{
		$this->call('get', 'bs', ['g' => ''])
			->assertRedirect(route('books'));
	}

	public function testBooksMainGenre()
	{
		$mainOldGenreGroupID = rand(1000, 10000);

		$genre = factory(Genre::class)->create();
		$genre->old_genre_group_id = $mainOldGenreGroupID;
		$genre->save();

		$genre2 = factory(Genre::class)->create();
		$genre2->old_genre_group_id = $mainOldGenreGroupID;
		$genre2->save();

		$this->call('get', 'bs', ['g' => 'g' . $genre->old_genre_group_id])
			->assertRedirect(route('books', ['genre' => [$genre->id, $genre2->id]]));
	}

	public function testBr()
	{
		$this->call('get', 'br', ['b' => 10, 'p' => 2])
			->assertRedirect(route('books.old.page', ['book' => 10, 'page' => 2]));
	}

	public function testBd()
	{
		$this->call('get', 'bd', ['b' => 10, 'p' => 2, 'CommentId' => 1235])
			->assertRedirect(route('books.show', ['book' => 10, 'page' => 2, 'comment' => 1235]));
	}

	public function testA()
	{
		$this->call('get', 'a', ['id' => 10])
			->assertRedirect(route('authors.show', ['author' => 10]));
	}

	public function testSeries()
	{
		$this->call('get', 'series')
			->assertRedirect(route('sequences'));
	}

	public function testBooksInSeries()
	{
		$this->call('get', 'books_in_series', ['id' => 10])
			->assertRedirect(route('sequences.show', ['sequence' => 10]));
	}

	public function testUsers()
	{
		$this->call('get', 'Users')
			->assertRedirect(route('users'));
	}

	public function testP()
	{
		$this->call('get', 'p', ['u' => 1, 'p' => 2, 'GoToBlogRecord' => 3])
			->assertRedirect(route('profile', ['user' => 1, 'page' => 2, 'blog' => 3]));
	}

	public function testForumRedirectToPost()
	{
		$this->call('get', 'ForumRedirectToPost', ['PostId' => 1])
			->assertRedirect(route('posts.go_to', ['post' => 1]));
	}

	public function testTopic()
	{
		$topic = factory(Topic::class)->create();

		$this->call('get', 'Topic', ['Id' => $topic->id, 'p' => 2, 'GoToPostId' => 3])
			->assertRedirect(route('topics.show', ['topic' => $topic->id, 'page' => 2, 'post' => 3]));

		$this->call('get', 'Topic')
			->assertNotFound();
	}

	public function testBookAddV2()
	{
		$this->call('get', 'BookAddV2')
			->assertRedirect(route('books.create'));
	}

	public function testUserBookRate()
	{
		$this->call('get', 'UserBookRate', ['UserId' => 1])
			->assertRedirect(route('users.votes', ['user' => 1]));
	}

	public function testEditProfile()
	{
		$this->call('get', 'edit_profile')
			->assertRedirect(route('home.latest_books'));

		$user = factory(User::class)->create();

		$this->actingAs($user)
			->call('get', 'edit_profile')
			->assertRedirect(route('users.edit', ['user' => $user]));
	}

	public function testUserComments()
	{
		$this->call('get', 'UserComments', ['UserId' => 1])
			->assertRedirect(route('users.books.comments', ['user' => 1]));
	}

	public function testUserLibBook()
	{
		$this->call('get', 'UserLibBook', ['UserId' => 1])
			->assertRedirect(route('users.books', ['user' => 1]));
	}

	public function testUserLibAuthor()
	{
		$this->call('get', 'UserLibAuthor', ['UserId' => 1])
			->assertRedirect(route('users.authors', ['user' => 1]));
	}

	public function testForum()
	{
		$this->call('get', 'Forum', ['Id' => ''])
			->assertRedirect(route('forums.index'));

		$forum = factory(Forum::class)->create();

		$this->call('get', 'Forum', ['Id' => $forum->id, 'p' => 2])
			->assertRedirect(route('forums.show', ['forum' => $forum->id, 'page' => 2]));

		$this->call('get', 'Forum')
			->assertRedirect(route('forums.index'));
	}

	public function testAs()
	{
		$this->call('get', 'as')
			->assertRedirect(route('authors'));
	}

	public function testAllGenre()
	{
		$this->call('get', 'all_genre')
			->assertRedirect(route('genres'));
	}

	public function testBookRateShow()
	{
		$this->call('get', 'BookRateShow', ['UserId' => 1])
			->assertRedirect(route('users.votes', ['user' => 1]));
	}

	public function testShowTalk()
	{
		$this->call('get', 'ShowTalk', ['UserId' => 1])
			->assertRedirect(route('users.messages.index', ['user' => 1]));
	}

	public function testMessageInbox()
	{
		$this->call('get', 'MessageInbox')
			->assertRedirect(route('books'));

		$user = factory(User::class)->create();

		$this->actingAs($user)
			->call('get', 'MessageInbox')
			->assertRedirect(route('users.inbox', ['user' => $user->id]));
	}

	public function testMainPage()
	{
		$this->call('get', 'main_page')
			->assertRedirect(route('home'));
	}

	public function testUserBookWithStatus()
	{
		$this->call('get', 'UserBookWithStatus', ['UserId' => 1, 'UserStatus' => 2])
			->assertRedirect(route('users.books.readed', ['user' => 1]));

		$this->call('get', 'UserBookWithStatus', ['UserId' => 1, 'UserStatus' => 3])
			->assertRedirect(route('users.books.read_later', ['user' => 1]));

		$this->call('get', 'UserBookWithStatus', ['UserId' => 1, 'UserStatus' => 4])
			->assertRedirect(route('users.books.read_now', ['user' => 1]));
	}

	public function testAuthorPageTabLoad()
	{
		$this->call('get', 'AuthorPageTabLoad', ['AuthorId' => 1])
			->assertRedirect(route('authors.show', ['author' => 1]));
	}

	public function testUserBooks()
	{
		$this->call('get', 'UserBooks', ['UserId' => 1])
			->assertRedirect(route('users.books.created', ['user' => 1]));
	}

	public function testUserPosts()
	{
		$this->call('get', 'UserPosts', ['UserId' => 1])
			->assertRedirect(route('users.posts', ['user' => 1]));
	}

	public function testUserLibSequence()
	{
		$this->call('get', 'UserLibSequence', ['UserId' => 1])
			->assertRedirect(route('users.sequences', ['user' => 1]));
	}

	public function testAllKeywords()
	{
		$this->call('get', 'AllKeywords')
			->assertRedirect(route('keywords.index'));
	}

	public function testForumPostSearch()
	{
		$topic = factory(Topic::class)->create();

		$this->call('get', 'ForumPostSearch', ['TopicId' => $topic->id, 'p' => 2])
			->assertRedirect(route('topics.posts.index', ['topic' => $topic->id, 'page' => 2]));

		$this->call('get', 'ForumPostSearch', ['p' => 2])
			->assertRedirect(route('home.latest_posts', ['page' => 2]));
	}

	public function testShowUsersVoteForBook()
	{
		$this->call('get', 'ShowUsersVoteForBook', ['BookId' => 1])
			->assertRedirect(route('books.votes', ['book' => 1]));
	}

	public function testMostViewedBooks()
	{
		$this->call('get', 'most_viewed_books', ['period' => 'today', 'p' => 2])
			->assertRedirect(route('home.popular_books', ['period' => 'day', 'page' => 2]));

		$this->call('get', 'most_viewed_books', ['period' => 'week', 'p' => 2])
			->assertRedirect(route('home.popular_books', ['period' => 'week', 'page' => 2]));

		$this->call('get', 'most_viewed_books', ['period' => 'month', 'p' => 2])
			->assertRedirect(route('home.popular_books', ['period' => 'month', 'page' => 2]));

		$this->call('get', 'most_viewed_books', ['period' => 'year', 'p' => 2])
			->assertRedirect(route('home.popular_books', ['period' => 'year', 'page' => 2]));

		$this->call('get', 'most_viewed_books', ['period' => 'all', 'p' => 2])
			->assertRedirect(route('books', ['order' => 'rating_year_desc', 'page' => 2]));

		$this->call('get', 'most_viewed_books', ['p' => 2])
			->assertRedirect(route('home.popular_books', ['period' => 'day', 'page' => 2]));
	}

	public function testAddBookFb2()
	{
		$this->call('get', 'add_book_fb2')
			->assertRedirect(route('books.create'));
	}

	public function testForums()
	{
		$this->call('get', 'Forums')
			->assertRedirect(route('forums.index'));
	}

	public function testBookRatingPeriod()
	{
		$this->call('get', 'BookRatingPeriod', ['period' => 'today', 'p' => 2])
			->assertRedirect(route('home.popular_books', ['period' => 'day', 'page' => 2]));

		$this->call('get', 'BookRatingPeriod', ['period' => 'week', 'p' => 2])
			->assertRedirect(route('home.popular_books', ['period' => 'week', 'page' => 2]));

		$this->call('get', 'BookRatingPeriod', ['period' => 'month', 'p' => 2])
			->assertRedirect(route('home.popular_books', ['period' => 'month', 'page' => 2]));

		$this->call('get', 'BookRatingPeriod', ['period' => 'year', 'p' => 2])
			->assertRedirect(route('home.popular_books', ['period' => 'year', 'page' => 2]));

		$this->call('get', 'BookRatingPeriod', ['period' => 'all', 'p' => 2])
			->assertRedirect(route('books', ['page' => 2]));

		$this->call('get', 'BookRatingPeriod', ['p' => 2])
			->assertRedirect(route('books', ['page' => 2]));
	}

	public function testUsersOnModerate()
	{
		$this->call('get', 'UsersOnModerate')
			->assertRedirect(route('users.on_moderation'));
	}

	public function testShowUsers()
	{
		$this->call('get', 'show_users')
			->assertRedirect(route('users'));
	}

	public function testAuthorComments()
	{
		$this->call('get', 'AuthorComments', ['AuthorId' => 1])
			->assertRedirect(route('authors.comments', ['author' => 1]));
	}

	public function testAuthorRateShow()
	{
		$this->call('get', 'AuthorRateShow', ['AuthorId' => 1])
			->assertRedirect(route('authors.books_votes', ['author' => 1]));
	}

	public function testComments()
	{
		$this->call('get', 'Comments', ['p' => 2])
			->assertRedirect(route('home.latest_comments', ['page' => 2]));
	}

	public function testBlogRecordRedirectTo()
	{
		$blog = factory(Blog::class)->create();

		$this->call('get', 'BlogRecordRedirectTo', ['Id' => $blog->id])
			->assertRedirect(route('users.blogs.go', ['user' => $blog->owner->id, 'blog' => $blog->id]));
	}

	public function testCommentRedirectTo()
	{
		$this->call('get', 'CommentRedirectTo', ['CommentId' => 1])
			->assertRedirect(route('comments.go', ['comment' => 1]));
	}

	public function testRules()
	{
		$this->call('get', 'Rules')
			->assertRedirect(route('rules'));
	}

	public function testForRightsOwners()
	{
		$this->call('get', 'ForRightsOwners')
			->assertRedirect(route('for_rights_owners'));
	}

	public function testWhoLike()
	{
		$this->call('get', 'WhoLike', ['Type' => 'ForumPost', 'Id' => 2])
			->assertRedirect(route('likes.users', ['type' => 'post', 'id' => 2]));

		$this->call('get', 'WhoLike', ['Type' => 'BlogRecord', 'Id' => 2])
			->assertRedirect(route('likes.users', ['type' => 'blog', 'id' => 2]));

		$this->call('get', 'WhoLike', ['Type' => 'Author', 'Id' => 2])
			->assertRedirect(route('likes.users', ['type' => 'author', 'id' => 2]));

		$this->call('get', 'WhoLike', ['Type' => 'Book', 'Id' => 2])
			->assertRedirect(route('likes.users', ['type' => 'book', 'id' => 2]));

		$this->call('get', 'WhoLike', ['Type' => 'Sequence', 'Id' => 2])
			->assertRedirect(route('likes.users', ['type' => 'sequence', 'id' => 2]));

		$this->call('get', 'WhoLike')
			->assertNotFound();
	}

	public function testAllBooks()
	{
		$this->call('get', 'AllBooks', ['kw' => 'Ведьмы'])
			->assertRedirect(route('books', ['kw' => 'Ведьмы']));
	}

	public function testWhoLikeWrongParametersFix()
	{
		$this->get('/WhoLike?Type=BlogRecord&amp%3BId=86597')
			->assertNotFound();
	}
}
