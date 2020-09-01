<?php

namespace Tests\Browser;

use App\Author;
use App\Comment;
use App\Enums\UserRelationType;
use App\User;
use App\UserRelation;
use Tests\DuskTestCase;

class CommentTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */
	public function testSeeWhoLikesOrDislikesComment()
	{
		$this->browse(function ($user_browser) {

			$user = factory(User::class)->create();
			$user->group->comment_view_who_likes_or_dislikes = true;
			$user->push();

			$comment = factory(Comment::class)
				->create([
					'create_user_id' => $user->id
				]);

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('comments.go', $comment))
				->whenAvailable('.item[data-id="' . $comment->id . '"]', function ($item) {
					$item->assertVisible('.dropdown-toggle')
						->click('.dropdown-toggle')
						->whenAvailable('.dropdown-menu.show', function ($menu) {
							$menu->assertSee(__('comment.who_likes'))
								->assertDontSee(__('comment.who_dislikes'));
						});
				});

			$user->group->comment_view_who_likes_or_dislikes = false;
			$user->push();

			$comment = factory(Comment::class)
				->create([
					'create_user_id' => $user->id
				]);

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('comments.go', $comment))
				->whenAvailable('.item[data-id="' . $comment->id . '"]', function ($item) {
					$item->assertVisible('.dropdown-toggle')
						->click('.dropdown-toggle')
						->whenAvailable('.dropdown-menu.show', function ($menu) {
							$menu->assertDontSee(__('comment.who_likes'))
								->assertDontSee(__('comment.who_dislikes'));
						});
				});

		});
	}

	public function testFriendsAndSubscribers()
	{
		$this->browse(function ($user_browser) {

			$user = factory(User::class)
				->create();

			$user_relation = factory(UserRelation::class)
				->create([
					'user_id' => $user->id,
					'status' => UserRelationType::Subscriber
				]);

			$user_relation2 = factory(UserRelation::class)
				->create([
					'user_id' => $user->id,
					'status' => UserRelationType::Friend
				]);

			$this->assertTrue($user->isSubscriberOf($user_relation->second_user));
			$this->assertFalse($user->isSubscriptionOf($user_relation->second_user));
			$this->assertTrue($user->isFriendOf($user_relation2->second_user));

			$comment = factory(Comment::class)
				->create(['create_user_id' => $user_relation->second_user->id]);

			$comment2 = factory(Comment::class)
				->create(['create_user_id' => $user_relation2->second_user->id]);

			$comment3 = factory(Comment::class)
				->create(['create_user_id' => $user->id]);

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('users.subscriptions.comments', $user))
				->assertSee($comment->text)
				->assertSee($comment2->text)
				->assertDontSee($comment3->text);

			$user_relation->status = UserRelationType::Null;
			$user_relation->save();

			$user_browser->visit(route('users.subscriptions.comments', $user))
				->assertDontSee($comment->text)
				->assertSee($comment2->text)
				->assertDontSee($comment3->text);
		});
	}

	public function testSeeAuthorBadge()
	{
		$this->browse(function ($user_browser) {

			$author = factory(Author::class)
				->states('with_author_manager', 'with_book')
				->create();

			$manager = $author->managers()->first();
			$user = $manager->user;
			$book = $author->books()->first();

			$comment = factory(Comment::class)
				->states('book')
				->create([
					'create_user_id' => $user->id,
					'commentable_type' => 'book',
					'commentable_id' => $book->id
				]);

			$user->attachAuthorGroup();

			$user_browser->resize(1000, 1500)
				->visit(route('books.show', $book))
				->with('.item[data-id="' . $comment->id . '"]', function ($comment_block) {
					$comment_block->with('.user-info', function ($info) {
						$info->assertVisible('.badge-author')
							->with('.badge-author', function ($badge) {
								$badge->assertSee(__('comment.create_user_book_author_type.Writer'));
							})
							->assertVisible('.groups')
							->with('.groups', function ($groups) {
								$groups->assertDontSee(__('author.manager_characters.author'));
							});
					});
				});
		});
	}
}