<?php

namespace Tests\Feature\Complain;

use App\Complain;
use App\User;
use Tests\TestCase;

class ComplainPolicyTest extends TestCase
{
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
}
