<?php

namespace Tests\Feature\User;

use App\Notifications\GroupAssignmentNotification;
use App\User;
use App\UserGroup;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserGroupTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testReplaceUserGroupsNotify()
	{
		Notification::fake();

		$admin = User::factory()->admin()->create();

		$user = User::factory()->create()->fresh();

		$group = UserGroup::factory()->notify_assignment()->create()->fresh();

		$group2 = UserGroup::factory()->notify_assignment()->create()->fresh();

		$text_status = $this->faker->realText(100);

		$response = $this->actingAs($admin)
			->patch(route('users.groups.update', ['user' => $user]),
				[
					'groups_id' => [
						$group->id,
						$group2->id
					],
					'text_status' => $text_status
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$user->refresh();

		$this->assertEquals(2, $user->groups()->count());
		$this->assertEquals([$group->id, $group2->id], $user->groups()->pluck('id')->toArray());
		$this->assertEquals($text_status, $user->text_status);

		$this->assertNotificationSentToUser($user, collect([$group, $group2]));

		$this->assertNotNull($user->groups()->first()->pivot->created_at);
		$this->assertNotNull($user->groups()->first()->pivot->updated_at);
	}

	public function assertNotificationSentToUser($user, $groups)
	{
		Notification::assertSentTo(
			$user,
			GroupAssignmentNotification::class,
			function ($notification, $channels) use ($user, $groups) {

				$this->assertContains('mail', $channels);
				$this->assertContains('database', $channels);

				$data = $notification->toArray($user);

				$name = implode(', ', $groups->pluck('name')->toArray());

				$this->assertEquals(__('notification.group_assigment.subject'), $data['title']);
				$this->assertEquals(__('notification.group_assigment.line', ['group_name' => $name]), $data['description']);
				$this->assertEquals(route('profile', ['user' => $user]), $data['url']);

				$mail = $notification->toMail($user);

				$this->assertEquals(__('notification.group_assigment.subject'), $mail->subject);
				$this->assertEquals(__('notification.group_assigment.line', ['group_name' => $name]), $mail->introLines[0]);
				$this->assertEquals(__('notification.group_assigment.action'), $mail->actionText);
				$this->assertEquals(route('profile', ['user' => $user]), $mail->actionUrl);

				return true;
			}
		);
	}

	public function testAttachUserGroupsNotify()
	{
		Notification::fake();

		$admin = User::factory()->admin()->create();

		$user = User::factory()->create()->fresh();

		$user->groups()->detach();
		$group = UserGroup::factory()->notify_assignment()->create()->fresh();
		$user->groups()->sync([$group->id]);

		$group1 = UserGroup::factory()->notify_assignment()->create()->fresh();

		$group2 = UserGroup::factory()->notify_assignment()->create()->fresh();

		$response = $this->actingAs($admin)
			->patch(route('users.groups.update', ['user' => $user]),
				[
					'groups_id' => [
						$group->id,
						$group1->id,
						$group2->id
					]
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$user->refresh();

		$this->assertEquals(3, $user->groups()->count());
		$this->assertEquals([$group->id, $group1->id, $group2->id], $user->groups()->pluck('id')->toArray());

		$this->assertNotificationSentToUser($user, collect([$group1, $group2]));
	}

	public function testDetachUserGroupsNotify()
	{
		Notification::fake();

		$admin = User::factory()->admin()->create();

		$user = User::factory()->create();

		$group = UserGroup::factory()->notify_assignment()->create()->fresh();
		$group1 = UserGroup::factory()->notify_assignment()->create()->fresh();
		$group2 = UserGroup::factory()->notify_assignment()->create()->fresh();

		$user->groups()->sync([$group->id, $group1->id, $group2->id]);

		$response = $this->actingAs($admin)
			->patch(route('users.groups.update', ['user' => $user]),
				[
					'groups_id' => [
						$group->id
					]
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$user->refresh();

		$this->assertEquals(1, $user->groups()->count());
		$this->assertEquals([$group->id], $user->groups()->pluck('id')->toArray());

		$this->assertNotficationNotSent($user);
	}

	public function assertNotficationNotSent($user)
	{
		Notification::assertSentTo(
			$user,
			GroupAssignmentNotification::class,
			function ($notification, $channels) use ($user) {

				$this->assertNotContains('mail', $channels);
				$this->assertNotContains('database', $channels);

				return true;
			}
		);
	}

	public function testAttachUserGroupsDontNotify()
	{
		Notification::fake();

		$admin = User::factory()->admin()->create();

		$user = User::factory()->create();

		$group1 = UserGroup::factory()->notify_assignment_disable()->create();

		$group2 = UserGroup::factory()->notify_assignment_disable()->create();

		$response = $this->actingAs($admin)
			->patch(route('users.groups.update', ['user' => $user]),
				[
					'groups_id' => [
						$group1->id,
						$group2->id
					]
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$user->refresh();

		$this->assertEquals([$group1->id, $group2->id], $user->groups()->pluck('id')->toArray());

		$this->assertNotficationNotSent($user);
	}

	public function testAttachUserGroupsNotifyOne()
	{
		Notification::fake();

		$admin = User::factory()->admin()->create();

		$user = User::factory()->create();

		$group1 = UserGroup::factory()->notify_assignment_disable()->create();

		$group2 = UserGroup::factory()->notify_assignment()->create();

		$response = $this->actingAs($admin)
			->patch(route('users.groups.update', ['user' => $user]),
				[
					'groups_id' => [
						$group1->id,
						$group2->id
					]
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$user->refresh();

		$this->assertEquals([$group1->id, $group2->id], $user->groups()->pluck('id')->toArray());

		$this->assertNotificationSentToUser($user, collect([$group2]));
	}

	public function testPermission()
	{
		$admin = User::factory()->create()->fresh();

		$user = User::factory()->create()->fresh();

		$this->assertFalse($admin->can('change_group', $user));

		$admin->group->change_users_group = true;
		$admin->push();

		$this->assertTrue($admin->can('change_group', $user));
	}

	public function testIndexHttp()
	{
		$admin = User::factory()->create();

		$response = $this
			->get(route('groups.index'))
			->assertStatus(401);

		$response = $this->actingAs($admin)
			->get(route('groups.index'))
			->assertForbidden();

		$admin->group->manage_users_groups = true;
		$admin->push();
		$admin->refresh();

		$response = $this->actingAs($admin)
			->get(route('groups.index'))
			->assertOk();
	}

	public function testEditHttp()
	{
		$admin = User::factory()->create();

		$group = UserGroup::factory()->create();

		$response = $this
			->get(route('groups.edit', ['group' => $group->id]))
			->assertStatus(401);

		$response = $this->actingAs($admin)
			->get(route('groups.edit', ['group' => $group->id]))
			->assertForbidden();

		$admin->group->manage_users_groups = true;
		$admin->push();
		$admin->refresh();

		$response = $this->actingAs($admin)
			->get(route('groups.edit', ['group' => $group->id]))
			->assertOk();
	}

	public function testEditNotFound()
	{
		$admin = User::factory()->create();

		$group = UserGroup::factory()->create();

		$id = $group->id;

		$group->forceDelete();

		$response = $this->actingAs($admin)
			->get(route('groups.edit', ['group' => $id]))
			->assertNotFound();
	}

	public function testDelete()
	{
		$admin = User::factory()->create();

		$group = UserGroup::factory()->create();

		$response = $this
			->delete(route('groups.destroy', ['group' => $group->id]))
			->assertStatus(401);

		$response = $this->actingAs($admin)
			->delete(route('groups.destroy', ['group' => $group->id]))
			->assertForbidden();

		$admin->group->manage_users_groups = true;
		$admin->push();
		$admin->refresh();

		$response = $this->actingAs($admin)
			->delete(route('groups.destroy', ['group' => $group->id]))
			->assertOk();

		$group->refresh();

		$this->assertSoftDeleted($group);

		$response = $this->actingAs($admin)
			->delete(route('groups.destroy', ['group' => $group->id]))
			->assertOk();

		$group->refresh();

		$this->assertFalse($group->trashed());
	}

	public function testCreateHttp()
	{
		$admin = User::factory()->create();

		$group = UserGroup::factory()->create();

		$response = $this
			->get(route('groups.create', ['group' => $group->id]))
			->assertStatus(401);

		$response = $this->actingAs($admin)
			->get(route('groups.create', ['group' => $group->id]))
			->assertForbidden();

		$admin->group->manage_users_groups = true;
		$admin->push();
		$admin->refresh();

		$response = $this->actingAs($admin)
			->get(route('groups.create', ['group' => $group->id]))
			->assertOk();
	}

	public function testStoreHttp()
	{
		$admin = User::factory()->create();

		$post = [
			'name' => $this->faker->realText(100),
			'not_show_ad' => true
		];

		$response = $this
			->post(route('groups.store'), $post)
			->assertStatus(401);

		$response = $this->actingAs($admin)
			->post(route('groups.store'), $post)
			->assertForbidden();

		$admin->group->manage_users_groups = true;
		$admin->push();
		$admin->refresh();

		$response = $this->actingAs($admin)
			->post(route('groups.store'), $post)
			->assertRedirect();

		$group = UserGroup::orderBy('id', 'desc')->limit(1)->get()->first();

		$this->assertEquals($post['name'], $group->name);
		$this->assertTrue($group->not_show_ad);
	}

	public function testUpdateHttp()
	{
		$admin = User::factory()->create();

		$group = UserGroup::factory()->create();

		$post = [
			'name' => $this->faker->realText(100),
			'not_show_ad' => true
		];

		$response = $this
			->patch(route('groups.update', $group->id), $post)
			->assertStatus(401);

		$response = $this->actingAs($admin)
			->patch(route('groups.update', $group->id), $post)
			->assertForbidden();

		$admin->group->manage_users_groups = true;
		$admin->push();
		$admin->refresh();

		$response = $this->actingAs($admin)
			->patch(route('groups.update', $group->id), $post)
			->assertRedirect();

		$group->refresh();

		$this->assertEquals($post['name'], $group->name);
		$this->assertTrue($group->not_show_ad);
	}

	public function testRelation()
	{
		$user = User::factory()->create();

		$group = UserGroup::factory()->create();

		$user->groups()->attach($group);

		$this->assertEquals(2, $user->groups()->count());
	}

	public function testOldRelation()
	{
		$user = User::factory()->create();

		$group = UserGroup::factory()->create();

		$user->group->manage_users_groups = true;
		$user->push();
		$user->refresh();

		$this->assertTrue($user->getPermission('manage_users_groups'));
		$this->assertTrue($user->getPermission('ManageUsersGroups'));

		$user->group->manage_users_groups = false;
		$user->push();
		$user->refresh();

		$this->assertFalse($user->getPermission('manage_users_groups'));
		$this->assertFalse($user->getPermission('ManageUsersGroups'));
		$this->assertFalse($user->getPermission('manage_users_groups_test'));
	}

	public function testChangeShowHttp()
	{
		$admin = User::factory()->create();

		$group = UserGroup::factory()->create(['show' => true]);

		$this->assertTrue($group->show);

		$post = [
			'name' => $this->faker->realText(100),
			'show' => false
		];

		$admin->group->manage_users_groups = true;
		$admin->push();
		$admin->refresh();

		$response = $this->actingAs($admin)
			->patch(route('groups.update', $group->id), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$group->refresh();

		$this->assertFalse($group->show);
	}

	public function testSeveralGroups()
	{
		$user = User::factory()->create();

		$group = UserGroup::factory()->create();
		$group->add_comment = true;
		$group->add_book = false;
		$group->add_forum_post = false;
		$group->save();

		$group2 = UserGroup::factory()->create();
		$group2->add_comment = false;
		$group2->add_book = true;
		$group2->add_forum_post = false;
		$group2->save();

		$group3 = UserGroup::factory()->create();
		$group3->add_comment = false;
		$group3->add_book = false;
		$group3->add_forum_post = false;
		$group3->add_book_without_check = true;
		$group3->save();

		$group4 = UserGroup::factory()->create();
		$group4->add_comment = false;
		$group4->add_book = false;
		$group4->add_forum_post = false;
		$group4->check_books = true;
		$group4->save();

		$user->groups()->sync([$group->id, $group2->id, $group3->id, $group4->id]);
		$user->refresh();

		$this->assertTrue($user->getPermission('add_comment'));
		$this->assertTrue($user->getPermission('add_book'));
		$this->assertFalse($user->getPermission('add_forum_post'));
		$this->assertTrue($user->getPermission('add_book_without_check'));
		$this->assertTrue($user->getPermission('check_books'));
	}

	public function testShowScope()
	{
		$group = UserGroup::factory()->create(['show' => true]);

		$this->assertEquals(1, UserGroup::where('id', $group->id)->show()->count());
		$this->assertEquals(1, UserGroup::where('id', $group->id)->count());

		$group->show = false;
		$group->save();

		$this->assertEquals(0, UserGroup::where('id', $group->id)->show()->count());
		$this->assertEquals(1, UserGroup::where('id', $group->id)->count());
	}

	public function testUserGroupStatus()
	{
		$user = User::factory()->create();

		$group = UserGroup::factory()->create(['show' => true]);

		$user->groups()->syncWithoutDetaching([$group->id]);

		$this->assertContains($group->name, $user->fresh()->getGroupStatus());

		$group->show = false;
		$group->save();

		$this->assertNotContains($group->name, $user->fresh()->getGroupStatus());
	}

	public function testUserGroupStatusRemoveVariable()
	{
		$title = Str::random(8);

		$group = UserGroup::factory()->create(['name' => $title]);

		$user = User::factory()->create();
		$user->text_status = __('author.manager_characters.author') . ', text';
		$user->attachUserGroup($group);
		$user->save();
		//dd($user->fresh()->getGroupStatus());
		$this->assertContains('text', $user->fresh()->getGroupStatus());
		$this->assertContains(__('author.manager_characters.author'), $user->fresh()->getGroupStatus());
		$this->assertNotContains(__('author.manager_characters.author'), $user->fresh()->getGroupStatus(__('author.manager_characters.author')));

		$this->assertContains($title, $user->fresh()->getGroupStatus());
		$this->assertNotContains($title, $user->fresh()->getGroupStatus($title));
	}

	public function testAttachUserGroupToUserIfNotExistsCommand()
	{
		$user = User::factory()->create();

		$user->groups()->detach();

		$this->assertEquals(0, $user->groups()->disableCache()->count());

		Artisan::call('user:attach_group_if_not_exists', ['latest_id' => $user->id]);

		$user->refresh();

		$this->assertEquals(1, $user->groups()->disableCache()->count());
	}

	public function testAttachUserGroupToUserIfNotExistsCommandDontAttachIfOtherGroupExists()
	{
		$user = User::factory()->create();

		$user->groups()->detach();

		$group = UserGroup::factory()->create()
			->fresh();

		$user->groups()->attach($group);

		$this->assertEquals(1, $user->groups()->disableCache()->count());
		$this->assertEquals($group->id, $user->groups()->disableCache()->get()->first()->id);

		Artisan::call('user:attach_group_if_not_exists', ['latest_id' => $user->id]);

		$this->assertEquals(1, $user->groups()->disableCache()->count());
		$this->assertEquals($group->id, $user->groups()->disableCache()->get()->first()->id);
	}

	public function testRemoveEqualTextStatusToGroupName()
	{
		$admin = User::factory()->admin()->create();

		$text = uniqid();

		$group = UserGroup::factory()->create(['name' => $text]);

		$user = User::factory()->create(['text_status' => $text]);

		$this->assertEquals($text, $group->name);
		$this->assertEquals($text, $user->text_status);

		$response = $this->actingAs($admin)
			->patch(route('users.groups.update', ['user' => $user]),
				[
					'groups_id' => [
						$group->id
					],
					'text_status' => $text
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$user->refresh();

		$this->assertEquals($group->id, $user->groups()->first()->id);
		$this->assertEquals('', $user->text_status);
	}

	public function testEmptyGroupsValidationError()
	{
		$admin = User::factory()->admin()->create();

		$user = User::factory()->create();

		$response = $this->actingAs($admin)
			->patch(route('users.groups.update', ['user' => $user]),
				[
					'groups_id' => []
				])
			->assertRedirect();
//dump(session('errors'));
		$response->assertSessionHasErrors(['groups_id' => __('validation.required', ['attribute' => __('user.groups_id')])]);
	}

	public function testAttachUserGroup()
	{
		$text = uniqid();
		$text2 = uniqid();

		$user = User::factory()->create(['text_status' => $text . ', ' . $text2]);

		$group = UserGroup::factory()->create(['name' => $text]);

		$this->assertEquals($text, $group->name);
		$this->assertEquals($text . ', ' . $text2, $user->text_status);

		$user->attachUserGroup($group);
		$user->save();
		$user->refresh();

		$this->assertTrue($user->groups()->where('id', $group->id)->exists());
		$this->assertEquals(2, $user->groups()->count());
		$this->assertEquals($text2, $user->text_status);

		$user->attachUserGroup($group);
		$user->attachUserGroup($group);
		$user->attachUserGroup($group);

		$this->assertTrue($user->groups()->where('id', $group->id)->exists());
		$this->assertEquals(2, $user->groups()->count());
		$this->assertEquals($text2, $user->text_status);

		$this->assertNotNull($user->groups()->first()->pivot->created_at);
		$this->assertNotNull($user->groups()->first()->pivot->updated_at);
	}

	public function testWhereName()
	{
		$uniqid = uniqid();

		$group = UserGroup::factory()->create(['name' => 'Текст ' . $uniqid]);

		$this->assertEquals(1, UserGroup::whereName('Текст ' . $uniqid)->count());

		$this->assertEquals(1, UserGroup::whereName('тЕКСТ ' . $uniqid)->count());

		$this->assertEquals(1, UserGroup::whereName('ТексТ ' . $uniqid)->count());

		$this->assertEquals(0, UserGroup::whereName('ТексТ  ' . $uniqid)->count());

		$this->assertEquals(0, UserGroup::whereName('ексТ ' . $uniqid)->count());
	}

	public function testAttachUserGroupByNameIfExists()
	{
		$name = $this->faker->realText(10);

		$group = UserGroup::factory()->create(['name' => $name]);

		$user = User::factory()->create();
		$user->attachUserGroupByNameIfExists($name);
		$user->refresh();

		$this->assertEquals($name, $user->groups()->whereName($name)->first()->name);
		$this->assertEquals(2, $user->groups()->count());

		$user->attachUserGroupByNameIfExists(uniqid());

		$this->assertEquals(2, $user->groups()->count());

		$this->assertNotNull($user->groups()->first()->pivot->created_at);
		$this->assertNotNull($user->groups()->first()->pivot->updated_at);
	}

	public function testAttachUserGroupNotification()
	{
		$user = User::factory()->create();

		Notification::fake();

		$group = UserGroup::factory()->notify_assignment()->create();

		$user->attachUserGroup($group);

		$this->assertNotificationSentToUser($user, collect([$group]));
	}

	public function testAttachUserGroupNotificationDisable()
	{
		Notification::fake();

		$user = User::factory()->create();

		$group = UserGroup::factory()->notify_assignment_disable()->create();

		$user->attachUserGroup($group);

		$this->assertNotficationNotSent($user);
	}

	public function testCantDeleteGroupIfGroupHasKey()
	{
		$group = UserGroup::factory()->create();
		$group->key = 'test';
		$group->save();

		$user = User::factory()->admin()->create();

		$this->assertFalse($user->can('delete', $group));
	}
}
