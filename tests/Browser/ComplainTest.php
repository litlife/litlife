<?php

namespace Tests\Browser;

use App\Complain;
use App\User;
use Tests\DuskTestCase;

class ComplainTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */
	public function testStartReview()
	{
		$this->browse(function ($browser) {

			$admin = factory(User::class)
				->states('admin')
				->create();

			$complain = factory(Complain::class)
				->states('sent_for_review')
				->create();

			$browser->resize(1000, 1000)
				->loginAs($admin)
				->visit(route('complaints.index'))
				->with('.complain[data-id="' . $complain->id . '"]', function ($item) {
					$item->assertVisible('.complain_buttons')
						->with('.complain_buttons', function ($buttons) {
							$buttons->assertSee(__('complain.start_review'))
								->clickLink(__('complain.start_review'));
						})
						->with('.status', function ($status) {
							$status->waitForText(__('complain.review_by_user'))
								->assertSee(__('complain.review_by_user'));
						});
				});

			$this->assertTrue($complain->fresh()->isReviewStarts());
		});
	}

	public function testStopReview()
	{
		$this->browse(function ($browser) {

			$admin = factory(User::class)
				->states('admin')
				->create();

			$complain = factory(Complain::class)
				->states('review_starts')
				->create();
			$complain->status_changed_user_id = $admin->id;
			$complain->save();

			$browser->resize(1000, 1000)
				->loginAs($admin)
				->visit(route('complaints.index'))
				->with('.complain[data-id="' . $complain->id . '"]', function ($item) {

					$item->with('.status', function ($status) {
						$status->assertSee(__('complain.review_by_user'));
					});

					$item->assertVisible('.complain_buttons')
						->with('.complain_buttons', function ($buttons) {
							$buttons->assertSee(__('complain.stop_review_request'))
								->clickLink(__('complain.stop_review_request'))
								->waitForText(__('complain.start_review'))
								->assertSee(__('complain.start_review'));
						});

					$item->with('.status', function ($status) {
						$status->assertDontSee(__('complain.review_by_user'));
					});
				});

			$this->assertTrue($complain->fresh()->isSentForReview());
		});
	}

	public function testAccepted()
	{
		$this->browse(function ($browser) {

			$admin = factory(User::class)
				->states('admin')
				->create();

			$complain = factory(Complain::class)
				->states('review_starts')
				->create();
			$complain->status_changed_user_id = $admin->id;
			$complain->save();

			$browser->resize(1000, 1000)
				->loginAs($admin)
				->visit(route('complaints.index'))
				->with('.complain[data-id="' . $complain->id . '"]', function ($item) {

					$item->with('.status', function ($status) {
						$status->assertSee(__('complain.review_by_user'));
					});

					$item->assertVisible('.complain_buttons')
						->with('.complain_buttons', function ($buttons) {
							$buttons->assertSee(__('complain.mark_as_reviewed'))
								->clickLink(__('complain.mark_as_reviewed'))
								->waitUntilMissing(__('complain.mark_as_reviewed'));
						});

					$item->with('.status', function ($status) {
						$status->waitForText(__('complain.complaint_was_reviewed'))
							->assertSee(__('complain.complaint_was_reviewed'))
							->assertDontSee(__('complain.review_by_user'));
					});
				});

			$this->assertTrue($complain->fresh()->isAccepted());
		});
	}
}
