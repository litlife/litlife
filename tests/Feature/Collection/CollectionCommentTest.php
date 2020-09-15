<?php

namespace Tests\Feature\Collection;

use App\Collection;
use App\Comment;
use App\Enums\StatusEnum;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class CollectionCommentTest extends TestCase
{
	public function testAddComment()
	{
		$user = factory(User::class)
			->create();
		$user->group->add_comment = true;
		$user->push();

		$collection = factory(Collection::class)
			->create(['who_can_comment' => 'everyone']);

		$text = $this->faker->realText(200);

		$response = $this->actingAs($user)
			->post(route('comments.store', ['commentable_type' => '18', 'commentable_id' => $collection->id]), [
				'bb_text' => $text
			]);

		$comment = $user->comments()->first();

		$response->assertRedirect(route('comments.go', $comment))
			->assertSessionHasNoErrors();

		$this->assertNotNull($comment);
		$this->assertEquals($text, $comment->bb_text);

		$response->assertRedirect(route('comments.go', $comment));

		$collection->refresh();

		$this->assertEquals(1, $collection->comments_count);
	}

	public function testRedirectToComment()
	{
		$comment = factory(Comment::class)
			->states('collection')
			->create();

		$this->assertTrue($comment->isCollectionType());

		$response = $this->get(route('comments.go', $comment))
			->assertRedirect(route('collections.comments', [
				'collection' => $comment->commentable,
				'page' => 1,
				'comment' => $comment,
				'#comment_' . $comment->id
			]));
	}

	public function testCommentsHttp()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$comment = factory(Comment::class)
			->states('collection')
			->create();

		$collection = $comment->commentable;
		$collection->status = StatusEnum::Accepted;
		$collection->who_can_add = 'everyone';
		$collection->who_can_comment = 'everyone';
		$collection->save();
		$collection->refresh();

		$this->actingAs($user)
			->get(route('collections.comments', $collection))
			->assertOk()
			->assertSeeText($comment->text)
			->assertSee(route('comments.store', [
				'commentable_type' => 18,
				'commentable_id' => $collection->id
			]));
	}

	public function testCommentsOkIfUserGuestCanSeeEveryone()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create();

		$this->get(route('collections.comments', $collection))
			->assertOk();
	}

	public function testCommentsForbiddenIfUserGuestCanSeeMe()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create();

		$this->get(route('collections.comments', $collection))
			->assertForbidden();
	}

	public function testToggleWithoutAjax()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create()
			->fresh();

		$admin = factory(User::class)
			->create();

		$response = $this->actingAs($admin)
			->followingRedirects()
			->get(route('collections.event_notification_subcriptions.toggle', $collection))
			->assertOk()
			->assertSeeText(__('collection.notifications_for_new_collection_comments_has_been_successfully_enabled', ['collection_title' => $collection->title]));

		$this->actingAs($admin)
			->followingRedirects()
			->get(route('collections.event_notification_subcriptions.toggle', $collection))
			->assertOk()
			->assertSeeText(__('collection.notifications_about_new_comments_to_the_collection_successfully_disabled', ['collection_title' => $collection->title]));
	}

	public function testReplyCreateIsOk()
	{
		$comment = factory(Comment::class)
			->states('collection')
			->create();

		$collection = $comment->commentable;
		$collection->who_can_comment = 'everyone';
		$collection->save();
		$collection->refresh();

		$user = factory(User::class)
			->states('admin')
			->create();

		$this->assertTrue($user->can('commentOn', $collection));
		$this->assertTrue($user->can('reply', $comment));

		$this->actingAs($user)
			->get(route('comments.create', [
				'commentable_type' => $comment->commentable_type,
				'commentable_id' => $comment->commentable_id,
				'parent' => $comment->id
			]))
			->assertOk();
	}

	public function testReplyStoreIsOk()
	{
		$comment = factory(Comment::class)
			->states('collection')
			->create();

		$collection = $comment->commentable;
		$collection->who_can_comment = 'everyone';
		$collection->save();
		$collection->refresh();

		$user = factory(User::class)
			->states('admin')
			->create();

		$text = Str::random(10);

		$this->actingAs($user)
			->post(route('comments.store', [
				'commentable_type' => $comment->commentable_type,
				'commentable_id' => $comment->commentable_id,
				'parent' => $comment->id
			]), ['bb_text' => $text])
			->assertRedirect();

		$reply = $comment->descendants($comment->id)->first();

		$this->assertNotNull($reply);
		$this->assertEquals($text, $reply->bb_text);
	}

	public function testCollectionComment()
	{
		$comment = factory(Comment::class)
			->states('collection')
			->create(['commentable_type' => 18]);

		$this->assertEquals(18, $comment->commentable_type);
		$this->assertEquals('Collection', $comment->getCommentableModelName());
		$this->assertTrue($comment->isCollectionType());
	}
}
