<?php

namespace Tests\Feature\Mailing;

use App\Mailing;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class MailingTest extends TestCase
{
	public function testStore()
	{
		$user = factory(User::class)->states('admin')->create();

		$email = $this->faker->email;
		$priority = 90;

		$email2 = $this->faker->email;
		$priority2 = 10;

		$text = $email . '     ' . $priority . "\n";
		$text .= $email2 . '             ' . $priority2;

		$this->actingAs($user)
			->post(route('mailings.store'), [
				'text' => $text
			])->assertRedirect()
			->assertSessionHas(['success' => 'Список обновлен']);

		$mailing1 = Mailing::whereEmail($email)->first();
		$mailing2 = Mailing::whereEmail($email2)->first();

		$this->assertNotNull($mailing1);
		$this->assertNotNull($mailing2);

		$this->assertEquals($email, $mailing1->email);
		$this->assertEquals($email2, $mailing2->email);

		$this->assertEquals($priority, $mailing1->priority);
		$this->assertEquals($priority2, $mailing2->priority);
	}

	public function testDontAddIfEmailAlreadyExists()
	{
		$user = factory(User::class)->states('admin')
			->create();

		$mailing = factory(Mailing::class)
			->create();

		$email = $mailing->email;
		$priority = 90;

		$text = $email . ' ' . $priority . "\n";

		$this->actingAs($user)
			->post(route('mailings.store'), [
				'text' => $text
			])->assertRedirect()
			->assertSessionHas(['success' => 'Список обновлен']);

		$this->assertEquals(1, Mailing::whereEmail($email)->count());

		$mailing2 = Mailing::whereEmail($email)->first();

		$this->assertEquals($email, $mailing2->email);
		$this->assertTrue($mailing->is($mailing2));
	}

	public function testStoreTextWithoutEmail()
	{
		$user = factory(User::class)->states('admin')
			->create();

		$this->actingAs($user)
			->post(route('mailings.store'), [
				'text' => '     '
			])->assertRedirect()
			->assertSessionHas(['success' => 'Список обновлен']);
	}

	public function testStoreWrongEmail()
	{
		$user = factory(User::class)->states('admin')
			->create();

		$email = Str::random(8);

		$this->actingAs($user)
			->post(route('mailings.store'), [
				'text' => $email . ' 90'
			])->assertRedirect()
			->assertSessionHas(['success' => 'Список обновлен']);

		$this->assertEquals(0, Mailing::whereEmail($email)->count());
	}

	public function testStoreEmailWithoutPriority()
	{
		$user = factory(User::class)->states('admin')
			->create();

		$email = $this->faker->email;
		$email2 = $this->faker->email;

		$this->actingAs($user)
			->post(route('mailings.store'), [
				'text' => $email . "\n" . $email2
			])->assertRedirect()
			->assertSessionHas(['success' => 'Список обновлен']);

		$this->assertEquals(1, Mailing::whereEmail($email)->count());
		$this->assertEquals(1, Mailing::whereEmail($email2)->count());
	}

	public function testSentWaitedScope()
	{
		$mailing = factory(Mailing::class)
			->create();

		$this->assertEquals(0, Mailing::whereEmail($mailing->email)->sent()->count());
		$this->assertEquals(1, Mailing::whereEmail($mailing->email)->waited()->count());

		$mailing->sent_at = now();
		$mailing->save();

		$this->assertEquals(1, Mailing::whereEmail($mailing->email)->sent()->count());
		$this->assertEquals(0, Mailing::whereEmail($mailing->email)->waited()->count());
	}

	public function testIsSent()
	{
		$mailing = factory(Mailing::class)
			->create();

		$this->assertFalse($mailing->isSent());

		$mailing->sent_at = now();
		$mailing->save();

		$this->assertTrue($mailing->isSent());
	}

	public function testIndexIsOk()
	{
		$user = factory(User::class)->states('admin')->create();

		$mailing = factory(Mailing::class)
			->create();

		$this->actingAs($user)
			->get(route('mailings.index'))
			->assertOk();
	}

	public function testStoreWithName()
	{
		$user = factory(User::class)->states('admin')
			->create();

		$email = $this->faker->email;
		$name = $this->faker->name;

		$text = $email . ' 10 ' . $name;

		$this->actingAs($user)
			->post(route('mailings.store'), [
				'text' => $text
			])->assertRedirect()
			->assertSessionHas(['success' => 'Список обновлен']);

		$mailing = Mailing::whereEmail($email)->first();

		$this->assertEquals($email, $mailing->email);
		$this->assertEquals($name, $mailing->name);
		$this->assertEquals(10, $mailing->priority);
	}

	public function testManagerMailersPolicy()
	{
		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('manage_mailings', User::class));

		$user->group->manage_mailings = true;
		$user->push();
		$user->refresh();

		$this->assertTrue($user->can('manage_mailings', User::class));
	}
}
