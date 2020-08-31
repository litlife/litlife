<?php

namespace Tests\Feature\Artisan;

use App\Comment;
use App\User;
use App\UserGroup;
use Tests\TestCase;

class AttachActiveCommentatorGroupToUsersTest extends TestCase
{
	private $group;

	public function testIsAttachedIfConditionMatch()
	{
		$user = factory(User::class)
			->create(['created_at' => now()->subMonths(7)]);

		$text = $this->faker->realText(10);

		$comments = factory(Comment::class, 2)
			->states('book')
			->create(['create_user_id' => $user->id, 'bb_text' => $text]);

		$user->refresh();

		$this->assertEquals(2, $user->comment_count);

		$this->executeCommandAttached($user, 6, 2);

		$this->assertUserHasGroup($user);
	}

	protected function executeCommandAttached(User $user, $min_months_from_the_date_of_registration, $min_comments_count)
	{
		$this->artisan('user:attach_active_commentator_group_to_users', [
			'min_months_from_the_date_of_registration' => $min_months_from_the_date_of_registration,
			'min_comments_count' => $min_comments_count,
			'id' => $user->id
		])
			->expectsOutput('Пользователь: ' . $user->id . '')
			->expectsOutput('Присоединяем группу ' . $this->group->name . ' к пользователю ' .
				$user->userName . ' ' . route('profile', $user))
			->assertExitCode(0);
	}

	protected function assertUserHasGroup(User $user)
	{
		$this->assertEquals(1, $user->groups()->disableCache()->whereName('Активный комментатор')->count());
	}

	public function testGroupIsNotAttachedIfTheAgeOfTheAccountIsLessThanTheDesired()
	{
		$user = factory(User::class)
			->create(['created_at' => now()->subMonths(5)]);

		$text = $this->faker->realText(10);

		$comments = factory(Comment::class, 3)
			->create(['create_user_id' => $user->id, 'bb_text' => $text]);

		$this->executeCommandNotAttached($user, 6, 2);

		$this->assertUserDontHasGroup($user);
	}

	protected function executeCommandNotAttached(User $user, $min_months_from_the_date_of_registration, $min_comments_count)
	{
		$this->artisan('user:attach_active_commentator_group_to_users', [
			'min_months_from_the_date_of_registration' => $min_months_from_the_date_of_registration,
			'min_comments_count' => $min_comments_count,
			'id' => $user->id
		])
			->assertExitCode(0);
	}

	protected function assertUserDontHasGroup(User $user)
	{
		$this->assertEquals(0, $user->groups()->disableCache()->whereName('Активный комментатор')->count());
	}

	public function testGroupIsNotAttachedIfTheNumberOfCommentsIsLessThanTheDesiredOne()
	{
		$user = factory(User::class)
			->create(['created_at' => now()->subMonths(7)]);

		$text = $this->faker->realText(10);

		$comments = factory(Comment::class, 1)
			->create(['create_user_id' => $user->id, 'bb_text' => $text]);

		$this->executeCommandNotAttached($user, 6, 2);

		$this->assertUserDontHasGroup($user);
	}

	public function testDontAttachAGroupIfTheUserIsAssignedACommentMasterGroup()
	{
		$user = factory(User::class)
			->create(['created_at' => now()->subMonths(8)]);

		$user->attachUserGroupByNameIfExists('Мастер комментария');
		$user->refresh();

		$this->assertTrue($user->hasUserGroup(UserGroup::whereName('Мастер комментария')->disableCache()->first()));

		$text = $this->faker->realText(10);

		$comments = factory(Comment::class, 3)
			->create(['create_user_id' => $user->id, 'bb_text' => $text]);

		$this->executeCommandNotAttached($user, 6, 2);

		$this->assertUserDontHasGroup($user);
	}

	protected function setUp(): void
	{
		parent::setUp();

		$this->group = UserGroup::whereName('Активный комментатор')->first();

		$this->assertNotNull($this->group);
	}
}
