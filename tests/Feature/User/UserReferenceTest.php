<?php

namespace Tests\Feature\User;

use App\Http\Middleware\UserReferenceCookieSave;
use App\Invitation;
use App\Notifications\NewReferredUserNotification;
use App\ReferredUser;
use App\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Litlife\Url\Url;
use Tests\TestCase;

class UserReferenceTest extends TestCase
{
	public function testUsersReferPageIsOkAuth()
	{
		$user = User::factory()->create();

		$this->actingAs($user)
			->get(route('users.refer'))
			->assertOk()
			->assertSeeText('Вашу уникальную ссылку вы можете скопировать из этого поля')
			->assertViewHas('user', $user)
			->assertViewHas('ref_name', config('litlife.name_user_refrence_get_param'))
			->assertViewHas('comission_from_refrence_buyer', config('litlife.comission_from_refrence_buyer'))
			->assertViewHas('comission_from_refrence_seller', config('litlife.comission_from_refrence_seller'));
	}

	public function testUsersReferPageIsOkForGuest()
	{
		$this->get(route('users.refer'))
			->assertOk()
			->assertSeeText("Для начала вам необходимо");
	}

	public function testReferredUserRelations()
	{
		$reference = ReferredUser::factory()->create()
			->fresh();

		$referred_by_user = $reference->referred_by_user;
		$referred_user = $reference->referred_user;

		$this->assertNotNull($referred_by_user);
		$this->assertNotNull($referred_user);

		$this->assertInstanceOf(User::class, $referred_user->referred_by_user->first());
	}

	public function testRelations()
	{
		$user = User::factory()->referred()->create(
			)
			->fresh();

		$referred_by_user = $user->referred_by_user->first();

		$this->assertNotNull($user->referred_by_user->first());
		$this->assertNotEquals($user->id, $referred_by_user->id);

		$this->assertNotNull($referred_by_user->refered_users->first());
		$this->assertNotNull($user->id, $referred_by_user->refered_users->first()->id);

		$referred_by_user->refer_users_refresh();
		$referred_by_user->refresh();

		$this->assertEquals(1, $referred_by_user->data->refer_users_count);
		//$this->assertEquals($user->id, $user->referred_by_user->refered_users->first()->id);
	}

	public function testSetReferredByUserId()
	{
		$comission_buy_book = rand(10, 50);
		$comission_sell_book = rand(10, 50);

		config(['litlife.comission_from_refrence_buyer' => $comission_buy_book]);
		config(['litlife.comission_from_refrence_seller' => $comission_sell_book]);

		$user = User::factory()->create();

		$referred_by_user = User::factory()->create();

		$user->setReferredByUserId($referred_by_user);
		$user->refresh();

		$this->assertEquals(1, $referred_by_user->refered_users()->count());
		$this->assertEquals($user->id, $referred_by_user->refered_users()->first()->id);

		$this->assertEquals($comission_buy_book, $referred_by_user->refered_users()->first()->pivot->comission_buy_book);
		$this->assertEquals($comission_sell_book, $referred_by_user->refered_users()->first()->pivot->comission_sell_book);
	}

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testCookieMiddleware()
	{
		//config(['litlife.name_user_refrence_get_param' => uniqid()]);
		$this->withMiddleware(UserReferenceCookieSave::class);

		$name = config('litlife.name_user_refrence_get_param');

		$user = User::factory()->create();

		$response = $this->get(route('home', [$name => $user->id, 'other' => 'value']))
			->assertRedirect((string)Url::fromString(route('home', ['other' => 'value']))->withPath('/'))
			->assertCookie($name, $user->id);
	}

	public function testRegisterThroughEmail()
	{
		Notification::fake();

		$name = config('litlife.name_user_refrence_get_param');

		$this->disableCookiesEncryption($name);

		$refer_user = User::factory()->create();

		$user = factory(User::class)
			->make()
			->toArray();

		$password = 'Abc' . rand(1000, 20000);
		$user['password'] = $password;
		$user['password_confirmation'] = $password;

		$invitation = Invitation::factory()->create();

		$response = $this->call('post', route('users.store', ['token' => $invitation->token]), $user, [$name => $refer_user->id]);
		if (session('errors')) dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect(route('welcome'));

		$user = User::orderBy('id', 'desc')->first();

		$this->assertEquals(1, $refer_user->fresh()->data->refer_users_count);
		$this->assertEquals($user->id, $refer_user->refered_users->first()->id);

		Notification::assertSentTo(
			$refer_user,
			NewReferredUserNotification::class,
			function ($notification, $channels) use ($user) {
				$this->assertContains('mail', $channels);
				$this->assertContains('database', $channels);

				$mail = $notification->toMail($user);

				$this->assertEquals(__('notification.new_refferd_user.subject'), $mail->subject);

				$this->assertEquals(__('notification.new_refferd_user.line', [
					'user_name' => $user->user_name
				]), $mail->introLines[0]);

				$this->assertEquals(__('notification.new_refferd_user.action'), $mail->actionText);

				$this->assertEquals(route('profile', $user), $mail->actionUrl);

				return $notification->user->id == $user->id;
			}
		);
	}

	public function testReferUsersPolicy()
	{
		$user = User::factory()->create();

		$this->assertTrue($user->can('refer_users', User::class));
	}

	public function testViewRefferedUsersPolicy()
	{
		$user = User::factory()->create();

		$this->assertTrue($user->can('view_referred_users', $user));

		$user2 = User::factory()->create();

		$this->assertFalse($user->can('view_referred_users', $user2));
	}

	public function testRouteUsersReferredUsers()
	{
		$nick = Str::random(8);

		$user = User::factory()->referred()->create(
			)
			->fresh();

		$referred_by_user = $user->referred_by_user->first();

		$this->actingAs($referred_by_user)
			->get(route('users.referred.users', $referred_by_user))
			->assertOk()
			->assertSeeText($nick);
	}

	public function testSeeReferedUsersCount()
	{
		$nick = Str::random(8);

		$user = User::factory()->referred()->create(
			)
			->fresh();

		$referred_by_user = $user->referred_by_user->first();
		$referred_by_user->refresh();

		$this->assertEquals(1, $referred_by_user->data->refer_users_count);

		$this->actingAs($referred_by_user)
			->get(route('users.refer'))
			->assertOk()
			->assertSeeText('Сколько вы привлекли: 1');
	}
}
