<?php

use App\AchievementUser;
use App\Enums\Gender;
use App\ReferredUser;
use App\User;
use App\UserEmail;
use App\UserPaymentDetail;
use App\UserPaymentTransaction;
use App\UserPhoto;
use App\UserPurchase;
use Faker\Generator as Faker;

$factory->define(App\User::class, function (Faker $faker) {
	static $password;

	return [
		'nick' => uniqid(),
		'last_name' => preg_replace('/(\'|\"|ё)/iu', '', $faker->lastName),
		'first_name' => preg_replace('/(\'|\"|ё)/iu', '', $faker->firstName),
		'email' => $faker->unique()->safeEmail,
		'password' => $password ?: $password = md0('password'),
		'gender' => Gender::male,
		'reg_ip' => $faker->ipv4,
		'born_date' => $faker->date('Y-m-d', '-20 years'),
		'city' => $faker->city
	];
});

$factory->afterCreating(App\User::class, function ($user, $faker) {

	$user->groups()->detach();

	$group = factory(App\UserGroup::class)
		->state('user')
		->create();

	$user->groups()->attach($group);

	$user->load('groups');

});

$factory->afterCreatingState(App\User::class, 'with_user_group', function ($user, $faker) {



});

$factory->afterCreatingState(App\User::class, 'with_user_permissions', function ($user, $faker) {

	$user->group->manage_collections = true;
	$user->group->blog = true;
	$user->group->display_technical_information = false;
	$user->group->admin_comment = false;
	$user->group->author_editor_request = true;
	$user->group->save();

});

$factory->afterCreatingState(App\User::class, 'without_email', function ($user, $faker) {
	$user->emails()->delete();
	$user->refreshConfirmedMailboxCount();
	$user->save();
});

$factory->afterCreatingState(App\User::class, 'with_confirmed_email', function ($user, $faker) {
	$email = factory(UserEmail::class)
		->create([
			'user_id' => $user->id,
			'notice' => true,
			'rescue' => true,
			'show_in_profile' => true,
			'confirm' => true
		]);
});

$factory->afterCreatingState(App\User::class, 'with_not_confirmed_email', function ($user, $faker) {

	foreach ($user->emails as $email) {
		$email->delete();
	}

	$email = factory(UserEmail::class)
		->state('not_confirmed')
		->create([
			'user_id' => $user->id
		]);
});

$factory->afterCreatingState(App\User::class, 'with_auth_log', function ($user, $faker) {

	$auth_log = factory(App\UserAuthLog::class)
		->create(['user_id' => $user->id]);
});

$factory->afterCreatingState(App\User::class, 'administrator', function ($user, $faker) {

	$user->groups()->detach();

	$group = factory(App\UserGroup::class)
		->state('administrator')
		->create();

	$user->groups()->attach($group);
	$user->load('groups');
});

$factory->afterCreatingState(App\User::class, 'admin', function ($user, $faker) {

	$user->groups()->detach();

	$group = factory(App\UserGroup::class)
		->state('administrator')
		->create();

	$user->groups()->attach($group);
	$user->load('groups');
});

$factory->afterCreatingState(App\User::class, 'with_thousand_money_on_balance', function ($user, $faker) {
	/*
		$transaction = new \App\UserPaymentTransaction;
		$transaction->user_id = $user->id;
		$transaction->sum = 1000;
		$transaction->typeDeposit();
		$transaction->
		$transaction->save();
		*/

	$payment = factory(UserPaymentTransaction::class)
		->state('incoming')
		->create(['sum' => 1000, 'user_id' => $user->id]);

	$user->balance(true);
});

$factory->afterCreatingState(App\User::class, 'with_100_balance', function ($user, $faker) {

	$payment = factory(UserPaymentTransaction::class)
		->state('incoming')
		->create(['sum' => 100, 'user_id' => $user->id]);

	$user->balance(true);
});

$factory->afterCreatingState(App\User::class, 'with_200_balance', function ($user, $faker) {

	$payment = factory(UserPaymentTransaction::class)
		->state('incoming')
		->create(['sum' => 200, 'user_id' => $user->id]);

	$user->balance(true);
});

$factory->afterCreatingState(App\User::class, 'with_300_balance', function ($user, $faker) {

	$payment = factory(UserPaymentTransaction::class)
		->state('incoming')
		->create(['sum' => 300, 'user_id' => $user->id]);

	$user->balance(true);
});

$factory->afterCreatingState(App\User::class, 'with_400_balance', function ($user, $faker) {

	$payment = factory(UserPaymentTransaction::class)
		->state('incoming')
		->create(['sum' => 400, 'user_id' => $user->id]);

	$user->balance(true);
});


$factory->afterCreatingState(App\User::class, 'with_thousand_earned_money_on_balance', function ($user, $faker) {

	$payment = factory(UserPurchase::class)
		->state('book')
		->create(['price' => 1000, 'seller_user_id' => $user->id, 'site_commission' => 0]);

	$user->balance(true);
});

$factory->afterCreatingState(App\User::class, 'with_wallet', function ($user, $faker) {

	$wallet = factory(UserPaymentDetail::class)
		->create(['user_id' => $user->id])
		->fresh();
});

$factory->afterCreatingState(App\User::class, 'with_purchased_book', function (User $user, $faker) {

	$purchase = factory(UserPurchase::class)
		->state('book')
		->create(['buyer_user_id' => $user->id]);
});

$factory->afterCreatingState(App\User::class, 'referred', function (User $user, $faker) {

	$referred_by_user = factory(User::class)
		->states('with_confirmed_email')
		->create();

	$referrer = factory(ReferredUser::class)
		->create([
			'referred_by_user_id' => $referred_by_user->id,
			'referred_user_id' => $user->id
		]);

	$referred_by_user->refer_users_refresh();
	$referred_by_user->save();
	$referred_by_user->refresh();
});

$factory->afterCreatingState(App\User::class, 'with_avatar', function (User $user, $faker) {

	$photo = factory(UserPhoto::class)
		->create(['user_id' => $user->id]);

	$user->avatar_id = $photo->id;
	$user->save();
});

$factory->afterMakingState(App\User::class, 'suspended', function (User $user, $faker) {
	$user->suspend();
});

$factory->afterCreatingState(App\User::class, 'with_achievement', function (User $user, $faker) {

	$achievementUser = factory(AchievementUser::class)
		->create(['user_id' => $user->id]);
});

$factory->state(App\User::class, 'male', function ($faker) {
	return [
		'gender' => 'male'
	];
});

$factory->state(App\User::class, 'female', function ($faker) {
	return [
		'gender' => 'female'
	];
});
