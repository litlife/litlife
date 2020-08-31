<?php

namespace Tests\Feature;

use App\Blog;
use App\Book;
use App\Comment;
use App\Complain;
use App\Post;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class ComplainTest extends TestCase
{
	public function testCreateIsOk()
	{
		$user = factory(User::class)->create();
		$user->group->complain = true;
		$user->push();

		$comment = factory(Comment::class)->create();

		$this->actingAs($user)
			->get(route('complains.report', ['type' => 'comment', 'id' => $comment->id]))
			->assertOk()
			->assertSeeText(__('complain.text'));
	}

	public function testStoreIsOk()
	{
		$user = factory(User::class)->create();
		$user->group->complain = true;
		$user->push();

		$comment = factory(Comment::class)->create();

		$count = Complain::getCachedOnModerationCount();

		$text = $this->faker->realText();

		$response = $this->actingAs($user)
			->post(route('complains.save', ['type' => 'comment', 'id' => $comment->id]), [
				'text' => $text
			])
			->assertSessionHasNoErrors()
			->assertRedirect()
			->assertSessionHas(['success' => __('complain.complaint_sent')]);

		$complain = $comment->complaints()->first();

		$response->assertRedirect(route('complaints.show', ['complain' => $complain]));

		$this->assertEquals(($count + 1), Complain::getCachedOnModerationCount());

		$this->assertNotNull($complain);
		$this->assertEquals($text, $complain->text);
		$this->assertEquals($user->id, $complain->create_user_id);
		$this->assertTrue($complain->isSentForReview());
	}

	public function testShowIsOkIfOnReview()
	{
		$user = factory(User::class)->create();

		$complain = factory(Complain::class)
			->states('comment', 'sent_for_review')
			->create();

		$user = $complain->create_user;
		$user->group->complain = true;
		$user->push();

		$this->actingAs($user)
			->get(route('complaints.show', $complain->id))
			->assertOk()
			->assertSeeText(__('complain.complaint_is_pending'));
	}

	public function testUpdateIsOk()
	{
		$user = factory(User::class)->create();

		$complain = factory(Complain::class)
			->states('comment', 'sent_for_review')
			->create();

		$user = $complain->create_user;
		$user->group->complain = true;
		$user->push();

		$text = $this->faker->realText();

		$this->actingAs($user)
			->post(route('complains.save', ['type' => $complain->complainable_type, 'id' => $complain->complainable_id]), [
				'text' => $text
			])
			->assertSessionHasNoErrors()
			->assertRedirect()
			->assertSessionHas(['success' => __('complain.complaint_was_successfully_edited')]);

		$complain->refresh();

		$this->assertStringContainsString($text, $complain->text);
	}

	public function testStartReviewHttp()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)
			->states('comment', 'sent_for_review')->create();

		$count = Complain::getCachedOnModerationCount();

		$this->actingAs($admin)
			->get(route('complains.start_review', $complain))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$complain->refresh();

		$this->assertEquals($count, Complain::getCachedOnModerationCount());
		$this->assertTrue($complain->isReviewStarts());
	}

	public function testAcceptHttp()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)->states('comment', 'review_starts')->create();
		$complain->status_changed_user_id = $admin->id;
		$complain->save();

		$count = Complain::getCachedOnModerationCount();

		$this->actingAs($admin)
			->get(route('complains.approve', $complain))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$complain->refresh();

		$this->assertEquals(($count - 1), Complain::getCachedOnModerationCount());
		$this->assertTrue($complain->isAccepted());
	}

	public function testStopReviewHttp()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)->states('comment', 'review_starts')->create();
		$complain->status_changed_user_id = $admin->id;
		$complain->save();

		$count = Complain::getCachedOnModerationCount();

		$this->actingAs($admin)
			->get(route('complains.stop_review', $complain))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$complain->refresh();

		$this->assertEquals($count, Complain::getCachedOnModerationCount());
		$this->assertTrue($complain->isSentForReview());
	}

	public function testIndexIfCommentDeleted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$comment = factory(Comment::class)
			->create();

		$complain = factory(Complain::class)
			->create([
				'complainable_type' => 'comment',
				'complainable_id' => $comment->id
			]);

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($comment->text)
			->assertSeeText($complain->text);

		$comment->delete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($comment->text)
			->assertSeeText($complain->text);

		$comment->forceDelete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($complain->text);
	}

	public function testIndexIfPostDeleted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)
			->states('post')
			->create();

		$post = $complain->complainable;

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($post->text)
			->assertSeeText($complain->text);

		$post->delete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($post->text)
			->assertSeeText($complain->text);

		$post->forceDelete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($complain->text);
	}

	public function testIndexIfWallPostDeleted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)
			->states('wall_post')
			->create();

		$wall_post = $complain->complainable;

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($wall_post->text)
			->assertSeeText($complain->text);

		$wall_post->delete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($wall_post->text)
			->assertSeeText($complain->text);

		$wall_post->forceDelete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($complain->text);
	}

	public function testPoliciesForReviewStarts()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)->states('comment', 'review_starts')->create();
		$complain->status_changed_user_id = $admin->id;
		$complain->save();

		$this->assertFalse($admin->can('startReview', $complain));
		$this->assertTrue($admin->can('approve', $complain));
		$this->assertTrue($admin->can('stopReview', $complain));

		$admin = factory(User::class)
			->states('admin')
			->create();

		$this->assertFalse($admin->can('startReview', $complain));
		$this->assertFalse($admin->can('approve', $complain));
		$this->assertFalse($admin->can('stopReview', $complain));
	}

	public function testPoliciesForSentForReview()
	{
		$complain = factory(Complain::class)
			->states('comment', 'sent_for_review')
			->create();

		$admin = factory(User::class)
			->states('admin')
			->create();

		$this->assertTrue($admin->can('startReview', $complain));
		$this->assertFalse($admin->can('approve', $complain));
		$this->assertFalse($admin->can('stopReview', $complain));
	}

	public function testCantComplainIfNoPermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->complain = false;
		$admin->push();

		$this->assertFalse($admin->can('create', Complain::class));
	}

	public function testCanComplainIfHasPermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->complain = true;
		$admin->push();

		$this->assertTrue($admin->can('create', Complain::class));
	}

	public function testGetComplainableName()
	{
		$complain = factory(Complain::class)
			->states('comment', 'sent_for_review')
			->create();

		$this->assertEquals('comment', $complain->getComplainableName());

		$post = factory(Post::class)->create();

		$complain->complainable_type = 'post';
		$complain->complainable_id = $post->id;
		$complain->save();
		$complain->refresh();

		$this->assertEquals('post', $complain->getComplainableName());

		$complain->complainable->forceDelete();
		$complain->refresh();

		$this->assertNull($complain->getComplainableName());
	}

	public function testUserCanViewComplainIfUserCreator()
	{
		$complain = factory(Complain::class)
			->states('comment', 'sent_for_review')->create();

		$this->assertTrue($complain->create_user->can('view', $complain));
	}

	public function testUserCanViewComplainIfUserCanReview()
	{
		$admin = factory(User::class)->create();
		$admin->group->complain_check = true;
		$admin->push();

		$complain = factory(Complain::class)
			->states('comment', 'sent_for_review')->create();

		$this->assertTrue($admin->can('view', $complain));
	}

	public function testUserCantViewComplainIfOtherUser()
	{
		$complain = factory(Complain::class)
			->states('comment', 'sent_for_review')->create();

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('view', $complain));
	}

	public function testUserCanViewOnReviewListIfCanCheck()
	{
		$admin = factory(User::class)->create();
		$admin->group->complain_check = true;
		$admin->push();

		$this->assertTrue($admin->can('viewOnReviewList', Complain::class));
	}

	public function testUserCantViewOnReviewListIfCanCheck()
	{
		$admin = factory(User::class)->create();

		$this->assertFalse($admin->can('viewOnReviewList', Complain::class));
	}

	public function testIndexComplainForBook()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)
			->states('book')
			->create();

		$this->assertInstanceOf(Book::class, $complain->complainable);

		$title = Str::random(10);

		$complain->complainable->title = $title;
		$complain->push();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($title);
	}

	public function testComplainIndexIsOkIfWallPostCreatorIsDeleted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)
			->states('wall_post')
			->create();

		$this->assertInstanceOf(Blog::class, $complain->complainable);

		$wall_post = $complain->complainable;

		$wall_post->owner->delete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk();
	}

	public function testComplainIndexIsOkIfWallPostCreateUserIsDeleted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)
			->states('wall_post')
			->create();

		$this->assertInstanceOf(Blog::class, $complain->complainable);

		$wall_post = $complain->complainable;

		$wall_post->create_user->delete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk();
	}

	public function testComplainShowStringAsID()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)
			->states('wall_post')
			->create();

		$this->actingAs($admin)
			->get(route('complaints.show', ['complain' => Str::random(5)]))
			->assertNotFound();
	}
}
