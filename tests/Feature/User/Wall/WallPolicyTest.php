<?php

namespace Tests\Feature\User\Wall;

use App\Blog;
use App\Enums\UserAccountPermissionValues;
use App\Enums\UserRelationType;
use App\User;
use App\UserRelation;
use Tests\TestCase;

class WallPolicyTest extends TestCase
{
	public function testWriteOnWallPolicy()
	{
		// if blacklist

		$relation = UserRelation::factory()->create(['status' => UserRelationType::Blacklist]);

		$active_user = $relation->first_user;
		$second_user = $relation->second_user;

		$this->assertTrue($active_user->hasAddedToBlacklist($second_user));

		$second_user->account_permissions->write_on_the_wall = UserAccountPermissionValues::everyone;
		$second_user->account_permissions->save();

		$this->assertFalse($active_user->can('writeOnWall', $second_user));
		$this->assertFalse($second_user->can('writeOnWall', $active_user));
		$this->assertTrue($second_user->can('writeOnWall', $second_user));

		$second_user->account_permissions->write_on_the_wall = UserAccountPermissionValues::friends;
		$second_user->account_permissions->save();

		$this->assertFalse($active_user->can('writeOnWall', $second_user));
		$this->assertFalse($second_user->can('writeOnWall', $active_user));
		$this->assertTrue($second_user->can('writeOnWall', $second_user));

		$second_user->account_permissions->write_on_the_wall = UserAccountPermissionValues::friends_and_subscribers;
		$second_user->account_permissions->save();

		$this->assertFalse($active_user->can('writeOnWall', $second_user));
		$this->assertFalse($second_user->can('writeOnWall', $active_user));
		$this->assertTrue($second_user->can('writeOnWall', $second_user));

		$second_user->account_permissions->write_on_the_wall = UserAccountPermissionValues::me;
		$second_user->account_permissions->save();

		$this->assertFalse($active_user->can('writeOnWall', $second_user));
		$this->assertFalse($second_user->can('writeOnWall', $active_user));
		$this->assertTrue($second_user->can('writeOnWall', $second_user));

		// if nobody

		$relation = UserRelation::factory()->create(['status' => UserRelationType::Null]);

		$active_user = $relation->first_user;
		$second_user = $relation->second_user;

		$this->assertTrue($active_user->isNobodyTo($second_user));
		$this->assertTrue($second_user->isNobodyTo($active_user));
		$this->assertTrue($second_user->can('writeOnWall', $second_user));

		$second_user->account_permissions->write_on_the_wall = UserAccountPermissionValues::everyone;
		$second_user->account_permissions->save();

		$this->assertTrue($active_user->can('writeOnWall', $second_user));
		$this->assertTrue($second_user->can('writeOnWall', $second_user));

		$second_user->account_permissions->write_on_the_wall = UserAccountPermissionValues::friends_and_subscribers;
		$second_user->account_permissions->save();

		$this->assertFalse($active_user->can('writeOnWall', $second_user));
		$this->assertTrue($second_user->can('writeOnWall', $second_user));

		$second_user->account_permissions->write_on_the_wall = UserAccountPermissionValues::friends;
		$second_user->account_permissions->save();

		$this->assertFalse($active_user->can('writeOnWall', $second_user));
		$this->assertTrue($second_user->can('writeOnWall', $second_user));


		// if subscriber

		$relation = UserRelation::factory()->create(['status' => UserRelationType::Subscriber]);

		$active_user = $relation->first_user;
		$second_user = $relation->second_user;

		$this->assertTrue($active_user->isSubscriberOf($second_user));
		$this->assertTrue($second_user->isSubscriptionOf($active_user));
		$this->assertTrue($second_user->can('writeOnWall', $second_user));

		$second_user->account_permissions->write_on_the_wall = UserAccountPermissionValues::everyone;
		$second_user->account_permissions->save();

		$this->assertTrue($active_user->can('writeOnWall', $second_user));
		$this->assertTrue($second_user->can('writeOnWall', $second_user));

		$second_user->account_permissions->write_on_the_wall = UserAccountPermissionValues::friends_and_subscribers;
		$second_user->account_permissions->save();

		$this->assertTrue($active_user->can('writeOnWall', $second_user));
		$this->assertTrue($second_user->can('writeOnWall', $second_user));

		$second_user->account_permissions->write_on_the_wall = UserAccountPermissionValues::friends;
		$second_user->account_permissions->save();

		$this->assertFalse($active_user->can('writeOnWall', $second_user));
		$this->assertTrue($second_user->can('writeOnWall', $second_user));


		// if friend

		$relation = UserRelation::factory()->create(['status' => UserRelationType::Friend]);

		$active_user = $relation->first_user;
		$second_user = $relation->second_user;

		$this->assertTrue($active_user->isFriendOf($second_user));

		$this->assertTrue($second_user->can('writeOnWall', $second_user));

		$second_user->account_permissions->write_on_the_wall = UserAccountPermissionValues::everyone;
		$second_user->account_permissions->save();

		$this->assertTrue($active_user->can('writeOnWall', $second_user));
		$this->assertTrue($second_user->can('writeOnWall', $second_user));

		$second_user->account_permissions->write_on_the_wall = UserAccountPermissionValues::friends;
		$second_user->account_permissions->save();

		$this->assertTrue($active_user->can('writeOnWall', $second_user));
		$this->assertTrue($second_user->can('writeOnWall', $second_user));

		$second_user->account_permissions->write_on_the_wall = UserAccountPermissionValues::friends_and_subscribers;
		$second_user->account_permissions->save();

		$this->assertTrue($active_user->can('writeOnWall', $second_user));
		$this->assertTrue($second_user->can('writeOnWall', $second_user));
	}

	public function testReplyPolicyIfInBlacklist()
	{
		$blog = Blog::factory()->create();

		$active_user = User::factory()->create();

		$relation = UserRelation::factory()->create([
				'user_id' => $blog->owner,
				'user_id2' => $active_user,
				'status' => UserRelationType::Blacklist
			]);

		$this->assertTrue($active_user->addedToBlacklistBy($blog->owner));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::everyone;
		$blog->owner->account_permissions->save();

		$this->assertFalse($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::friends;
		$blog->owner->account_permissions->save();

		$this->assertFalse($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::friends_and_subscribers;
		$blog->owner->account_permissions->save();

		$this->assertFalse($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::me;
		$blog->owner->account_permissions->save();

		$this->assertFalse($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::friends_and_subscriptions;
		$blog->owner->account_permissions->save();

		$this->assertFalse($active_user->can('reply', $blog));
	}

	public function testReplyPolicyIfNobody()
	{
		$blog = Blog::factory()->create();

		$active_user = User::factory()->create();

		$relation = UserRelation::factory()->create([
				'user_id' => $active_user,
				'user_id2' => $blog->owner,
				'status' => UserRelationType::Null
			]);

		$this->assertTrue($active_user->isNobodyTo($blog->owner));
		$this->assertTrue($blog->owner->isNobodyTo($active_user));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::everyone;
		$blog->owner->account_permissions->save();

		$this->assertTrue($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::friends_and_subscribers;
		$blog->owner->account_permissions->save();

		$this->assertFalse($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::friends;
		$blog->owner->account_permissions->save();

		$this->assertFalse($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::me;
		$blog->owner->account_permissions->save();

		$this->assertFalse($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::friends_and_subscriptions;
		$blog->owner->account_permissions->save();

		$this->assertFalse($active_user->can('reply', $blog));
	}

	public function testReplyPolicyIfSubscriber()
	{
		$blog = Blog::factory()->create();

		$active_user = User::factory()->create();

		$relation = UserRelation::factory()->create([
				'user_id' => $active_user,
				'user_id2' => $blog->owner,
				'status' => UserRelationType::Subscriber
			]);

		$this->assertTrue($active_user->isSubscriberOf($blog->owner));
		$this->assertTrue($blog->owner->isSubscriptionOf($active_user));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::everyone;
		$blog->owner->account_permissions->save();

		$this->assertTrue($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::friends_and_subscribers;
		$blog->owner->account_permissions->save();

		$this->assertTrue($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::friends;
		$blog->owner->account_permissions->save();

		$this->assertFalse($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::me;
		$blog->owner->account_permissions->save();

		$this->assertFalse($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::friends_and_subscriptions;
		$blog->owner->account_permissions->save();

		$this->assertFalse($active_user->can('reply', $blog));
	}

	public function testReplyPolicyIfFriend()
	{
		$blog = Blog::factory()->create();

		$active_user = User::factory()->create();

		$relation = UserRelation::factory()->create([
				'user_id' => $active_user,
				'user_id2' => $blog->owner,
				'status' => UserRelationType::Friend
			]);

		$this->assertTrue($active_user->isFriendOf($blog->owner));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::everyone;
		$blog->owner->account_permissions->save();

		$this->assertTrue($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::friends;
		$blog->owner->account_permissions->save();

		$this->assertTrue($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::friends_and_subscribers;
		$blog->owner->account_permissions->save();

		$this->assertTrue($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::me;
		$blog->owner->account_permissions->save();

		$this->assertFalse($active_user->can('reply', $blog));

		$blog->owner->account_permissions->comment_on_the_wall = UserAccountPermissionValues::friends_and_subscriptions;
		$blog->owner->account_permissions->save();

		$this->assertTrue($active_user->can('reply', $blog));
	}

	public function testAdminDeleteOrRestorePermissions()
	{
		$admin = User::factory()->with_user_group()->create();
		$admin->group->blog_other_user = true;
		$admin->push();

		$user = User::factory()->with_user_permissions()->create();

		$blog = Blog::factory()->create([
				'blog_user_id' => $user->id,
				'create_user_id' => $user->id,
			]);

		$this->assertTrue($admin->can('delete', $blog));
		$this->assertFalse($admin->can('restore', $blog));

		$blog->delete();
		$blog->fresh();

		$this->assertTrue($blog->trashed());
		$this->assertFalse($admin->can('delete', $blog));
		$this->assertTrue($admin->can('restore', $blog));

		$blog->restore();
		$blog->fresh();

		$this->assertFalse($blog->trashed());
		$this->assertTrue($admin->can('delete', $blog));
		$this->assertFalse($admin->can('restore', $blog));
	}

	public function testViewOnCheckPolicy()
	{
		$user = User::factory()->create();

		$this->assertFalse($user->can('viewOnCheck', Blog::class));

		$user->group->check_post_comments = true;
		$user->push();

		$this->assertTrue($user->can('viewOnCheck', Blog::class));
	}

	public function testApprovePolicy()
	{
		$user = User::factory()->create();

		$blog = Blog::factory()->create();

		$this->assertFalse($user->can('approve', $blog));

		$user->group->check_post_comments = true;
		$user->push();

		$this->assertFalse($user->can('approve', $blog));

		$blog->statusSentForReview();
		$blog->save();

		$this->assertTrue($user->can('approve', $blog));
	}
}
