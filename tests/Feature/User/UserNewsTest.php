<?php

namespace Tests\Feature\User;

use App\Blog;
use App\Enums\UserRelationType;
use App\User;
use App\UserRelation;
use Tests\TestCase;

class UserNewsTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testIfSubscriptionCreateWallMessageOnOwnWallHttp()
	{
		$auth_user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Subscriber
			]);

		$auth_user->isSubscriberOf($user);
		$user->isSubscriptionOf($auth_user);

		$blog = Blog::factory()->create([
				'blog_user_id' => $user->id,
				'create_user_id' => $user->id
			]);

		$this->assertEquals(1, $auth_user->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->getNotViewedFriendsNewsCount());

		$this->actingAs($auth_user)
			->get(route('news'))
			->assertSeeText($blog->text);

		$this->assertEquals(0, $auth_user->getNotViewedFriendsNewsCount());
	}

	public function testIfFriendCreateWallMessageOnOwnWallHttp()
	{
		$auth_user = User::factory()->create(['created_at' => now()->subMinute()]);


		$user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Friend
			]);

		$blog = Blog::factory()->create([
				'blog_user_id' => $user->id,
				'create_user_id' => $user->id
			]);

		$this->assertEquals(1, $auth_user->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->getNotViewedFriendsNewsCount());

		$this->actingAs($auth_user)
			->get(route('news'))
			->assertSeeText($blog->text);

		$this->assertEquals(0, $auth_user->getNotViewedFriendsNewsCount());
	}

	public function testIfSomeoneWriteOnSubscriptionWallHttp()
	{
		$auth_user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Subscriber
			]);

		$blog = Blog::factory()->create(['blog_user_id' => $user->id]);

		$this->assertEquals(1, $auth_user->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->getNotViewedFriendsNewsCount());

		$this->actingAs($auth_user)
			->get(route('news'))
			->assertSeeText($blog->text);

		$this->assertEquals(0, $auth_user->getNotViewedFriendsNewsCount());
	}

	public function testIfSomeoneWriteOnFriendWallHttp()
	{
		$auth_user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Friend
			]);

		$blog = Blog::factory()->create(['blog_user_id' => $user->id]);

		$this->assertEquals(1, $auth_user->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->getNotViewedFriendsNewsCount());

		$this->actingAs($auth_user)
			->get(route('news'))
			->assertSeeText($blog->text);

		$this->assertEquals(0, $auth_user->getNotViewedFriendsNewsCount());
	}

	public function testIfSomeoneReplyOnSubscriptionWallHttp()
	{
		$auth_user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Subscriber
			]);

		$blog = Blog::factory()->create([
				'blog_user_id' => $user->id,
				'create_user_id' => $user->id
			]);

		$reply = Blog::factory()->create([
				'parent' => $blog->id,
				'blog_user_id' => $user->id
			]);

		$this->assertEquals(2, $auth_user->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->getNotViewedFriendsNewsCount());

		$this->actingAs($auth_user)
			->get(route('news'))
			->assertSeeText($reply->text);

		$this->assertEquals(0, $auth_user->getNotViewedFriendsNewsCount());
	}

	public function testIfSomeoneReplyOnFriendWallHttp()
	{
		$auth_user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Friend
			]);

		$blog = Blog::factory()->create([
				'blog_user_id' => $user->id,
				'create_user_id' => $user->id
			]);

		$reply = Blog::factory()->create([
				'parent' => $blog->id,
				'blog_user_id' => $user->id
			]);

		$this->assertEquals(2, $auth_user->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->getNotViewedFriendsNewsCount());

		$this->actingAs($auth_user)
			->get(route('news'))
			->assertSeeText($reply->text);

		$this->assertEquals(0, $auth_user->getNotViewedFriendsNewsCount());
	}

	public function testIfSubscriptionWriteOnSomeoneWallHttp()
	{
		$auth_user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Subscriber
			]);

		$blog = Blog::factory()->create(['create_user_id' => $user->id]);

		$this->assertEquals(0, $auth_user->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->getNotViewedFriendsNewsCount());

		$this->actingAs($auth_user)
			->get(route('news'))
			->assertDontSeeText($blog->text);

		$this->assertEquals(0, $auth_user->getNotViewedFriendsNewsCount());
	}

	public function testIfFriendWriteOnSomeoneWallHttp()
	{
		$auth_user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Friend
			]);

		$blog = Blog::factory()->create(['create_user_id' => $user->id]);

		$this->assertEquals(0, $auth_user->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->getNotViewedFriendsNewsCount());

		$this->actingAs($auth_user)
			->get(route('news'))
			->assertDontSeeText($blog->text);

		$this->assertEquals(0, $auth_user->getNotViewedFriendsNewsCount());
	}


	public function testIfAuthUserWriteOnSubscriptionWallHttp()
	{
		$auth_user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Subscriber
			]);

		$blog = Blog::factory()->create([
				'blog_user_id' => $user->id,
				'create_user_id' => $auth_user->id
			]);

		$this->assertEquals(0, $auth_user->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->getNotViewedFriendsNewsCount());

		$this->actingAs($auth_user)
			->get(route('news'))
			->assertDontSeeText($blog->text);

		$this->assertEquals(0, $auth_user->getNotViewedFriendsNewsCount());
	}

	public function testIfAuthUserReplyOnSubscriptionWallHttp()
	{
		$auth_user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Subscriber
			]);

		$blog = Blog::factory()->create([
				'blog_user_id' => $user->id,
				'create_user_id' => $user->id
			]);

		$this->assertEquals(1, $auth_user->getNotViewedFriendsNewsCount());

		$this->actingAs($auth_user)
			->get(route('news'))
			->assertSeeText($blog->text);

		$reply = Blog::factory()->create([
				'parent' => $blog,
				'blog_user_id' => $user->id,
				'create_user_id' => $auth_user->id
			]);

		$this->assertEquals(0, $auth_user->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->getNotViewedFriendsNewsCount());

		$this->actingAs($auth_user)
			->get(route('news'))
			->assertDontSeeText($reply->text);

		$this->assertEquals(0, $auth_user->getNotViewedFriendsNewsCount());
	}

	public function testIfSubscriptionCreateNewMessageThenDeleteHttp()
	{
		$auth_user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Subscriber
			]);

		$blog = Blog::factory()->create([
				'blog_user_id' => $user->id,
				'create_user_id' => $user->id
			]);

		$this->assertEquals(1, $auth_user->fresh()->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->fresh()->getNotViewedFriendsNewsCount());

		$blog->delete();

		$this->assertEquals(0, $auth_user->fresh()->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->fresh()->getNotViewedFriendsNewsCount());
	}

	public function testIfSomeoneWriteOnSubscriptionWallThenDeleteHttp()
	{
		$auth_user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Subscriber
			]);

		$blog = Blog::factory()->create(['blog_user_id' => $user->id]);

		$this->assertEquals(1, $auth_user->fresh()->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->fresh()->getNotViewedFriendsNewsCount());

		$blog->delete();

		$this->assertEquals(0, $auth_user->fresh()->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->fresh()->getNotViewedFriendsNewsCount());

	}

	public function testIfSomeoneReplyOnSubscriptionWallThenDeleteThanRestoreHttp()
	{
		$auth_user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user = User::factory()->create(['created_at' => now()->subMinute()]);

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Subscriber
			]);

		$blog = Blog::factory()->create([
				'blog_user_id' => $user->id,
				'create_user_id' => $user->id
			]);

		$reply = Blog::factory()->create([
				'parent' => $blog->id,
				'blog_user_id' => $user->id
			]);

		$this->assertEquals(2, $auth_user->fresh()->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->fresh()->getNotViewedFriendsNewsCount());

		$reply->delete();

		$this->assertEquals(1, $auth_user->fresh()->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->fresh()->getNotViewedFriendsNewsCount());

		$reply->restore();

		$this->assertEquals(2, $auth_user->fresh()->getNotViewedFriendsNewsCount());
		$this->assertEquals(0, $user->fresh()->getNotViewedFriendsNewsCount());

		$this->actingAs($auth_user)
			->get(route('news'))
			->assertSeeText($blog->text);

		$this->actingAs($auth_user)
			->get(route('news'))
			->assertSeeText($reply->text);

		$this->assertEquals(0, $auth_user->fresh()->getNotViewedFriendsNewsCount());
	}
}
