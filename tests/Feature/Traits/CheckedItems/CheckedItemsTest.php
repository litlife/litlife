<?php

namespace Tests\Feature\Traits\CheckedItems;

use App\User;
use Tests\TestCase;

class CheckedItemsTest extends TestCase
{
	public function testOnReview()
	{
		$user = factory(User::class)
			->create();

		$this->be($user);

		$model = new TestModel();
		$model->statusPrivate();

		$this->assertTrue($model->isStatus('Private'));
		$this->assertTrue($model->is_private);
		$this->assertTrue($model->isPrivate());
		$this->assertNotNull($model->status_changed_at);
		$this->assertEquals($model->status_changed_user_id, $user->id);
	}

	public function testSentForReview()
	{
		$user = factory(User::class)
			->create();

		$this->be($user);

		$model = new TestModel();
		$model->statusSentForReview();

		$this->assertTrue($model->isStatus('OnReview'));
		$this->assertTrue($model->is_sent_for_review);
		$this->assertTrue($model->isSentForReview());
		$this->assertNotNull($model->status_changed_at);
		$this->assertEquals($model->status_changed_user_id, $user->id);
	}

	public function testAccepted()
	{
		$user = factory(User::class)
			->create();

		$this->be($user);

		$model = new TestModel();
		$model->statusReject();
		$model->statusAccepted();

		$this->assertTrue($model->isStatus('Accepted'));
		$this->assertTrue($model->is_accepted);
		$this->assertTrue($model->isAccepted());
		$this->assertNotNull($model->status_changed_at);
		$this->assertEquals($model->status_changed_user_id, $user->id);
	}

	public function testRejected()
	{
		$user = factory(User::class)
			->create();

		$this->be($user);

		$model = new TestModel();
		$model->statusReject();

		$this->assertTrue($model->isStatus('Rejected'));
		$this->assertTrue($model->is_rejected);
		$this->assertTrue($model->isRejected());
		$this->assertNotNull($model->status_changed_at);
		$this->assertEquals($model->status_changed_user_id, $user->id);
	}
}
