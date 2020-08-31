<?php

namespace Tests\Feature\Collection;

use App\Book;
use App\CollectedBook;
use App\Collection;
use App\CollectionUser;
use App\Enums\StatusEnum;
use App\Enums\UserAccountPermissionValues;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class CollectionTest extends TestCase
{
	public function testIndexHttp()
	{
		$collection = factory(Collection::class)
			->create();

		$user = $collection->create_user;

		$this->actingAs($user)
			->get(route('collections.index'))
			->assertOk();
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testCreateHttp()
	{
		$user = factory(User::class)->states('with_user_permissions')->create();

		$this->actingAs($user)
			->get(route('collections.create'))
			->assertOk();
	}

	public function testStoreHttp()
	{
		$user = factory(User::class)->states('with_user_permissions')->create();

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'status' => 3,
			'who_can_add' => 'me',
			'who_can_comment' => 'me',
			'url' => $this->faker->url,
			'url_title' => $this->faker->realText(50),
		];

		$response = $this->actingAs($user)
			->post(route('collections.store', $post))
			->assertSessionHasNoErrors();
		//dump(session('errors'));
		$response->assertRedirect(route('users.collections.created', $user));

		$collection = $user->created_collections()->first();

		$this->assertEquals($post['title'], $collection->title);
		$this->assertEquals($post['description'], $collection->description);
		$this->assertTrue($collection->isPrivate());
		$this->assertEquals(UserAccountPermissionValues::getValue($post['who_can_add']), $collection->who_can_add);
		$this->assertEquals(UserAccountPermissionValues::getValue($post['who_can_comment']), $collection->who_can_comment);
		$this->assertEquals($post['url'], $collection->url);
		$this->assertEquals($post['url_title'], $collection->url_title);
		$this->assertEquals(1, $collection->users_count);

		$user->refresh();

		$this->assertEquals(1, $user->data->created_collections_count);
	}

	public function testStoreNotPrivateHttp()
	{
		$user = factory(User::class)->states('with_user_permissions')->create();

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'status' => 0,
			'who_can_add' => UserAccountPermissionValues::getRandomKey(),
			'who_can_comment' => UserAccountPermissionValues::getRandomKey(),
			'url' => $this->faker->url,
			'url_title' => $this->faker->realText(50),
		];

		$response = $this->actingAs($user)
			->post(route('collections.store', $post))
			->assertSessionHasNoErrors();
		//dump(session('errors'));
		$response->assertRedirect(route('users.collections.created', $user));

		$collection = $user->created_collections()->first();

		$this->assertTrue($collection->isAccepted());
	}

	public function testEditHttp()
	{
		$collection = factory(Collection::class)->create();

		$this->actingAs($collection->create_user)
			->get(route('collections.edit', $collection))
			->assertOk();
	}

	public function testUpdateHttp()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create();

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'status' => StatusEnum::Private,
			'who_can_add' => 'me',
			'who_can_comment' => 'me',
			'url' => $this->faker->url,
			'url_title' => $this->faker->realText(50),
		];

		$this->actingAs($collection->create_user)
			->followingRedirects()
			->patch(route('collections.update', ['collection' => $collection]), $post)
			->assertOk()
			->assertSeeText(__('collection.data_successfully_updated'));

		$collection->refresh();

		$this->assertEquals($post['title'], $collection->title);
		$this->assertEquals($post['description'], $collection->description);
		$this->assertTrue($collection->isPrivate());
		$this->assertEquals(UserAccountPermissionValues::getValue($post['who_can_add']), $collection->who_can_add);
		$this->assertEquals(UserAccountPermissionValues::getValue($post['who_can_comment']), $collection->who_can_comment);
		$this->assertEquals($post['url'], $collection->url);
		$this->assertEquals($post['url_title'], $collection->url_title);
	}

	public function testDeleteHttp()
	{
		$collection = factory(Collection::class)->create();

		$user = $collection->create_user;

		$this->actingAs($collection->create_user)
			->delete(route('collections.destroy', $collection))
			->assertOk();

		$collection->refresh();
		$user->refresh();

		$this->assertEquals(0, $user->data->created_collections_count);

		$this->assertSoftDeleted($collection);

		$this->actingAs($collection->create_user)
			->delete(route('collections.destroy', $collection))
			->assertOk();

		$collection->refresh();
		$this->assertFalse($collection->trashed());

		$user->refresh();
		$this->assertEquals(1, $user->data->created_collections_count);
	}

	public function testUpdateHttpSeeEnumValidationErrors()
	{
		$collection = factory(Collection::class)->create();

		$post = [
			'title' => $this->faker->realText(100),
			'status' => 'test',
			'who_can_see' => 'test',
			'who_can_add' => 'test'
		];

		$response = $this->actingAs($collection->create_user)
			->patch(route('collections.update', ['collection' => $collection]), $post)
			->assertStatus(302);
		//dump(session('errors'));
		$response->assertSessionHasErrors([
			'status' => __('validation.in', ['attribute' => __('collection.status')]),
			'who_can_add' => __('validation.enum_key', ['attribute' => __('collection.who_can_add')]),
			'who_can_comment' => __('validation.required', ['attribute' => __('collection.who_can_comment')]),
		]);
	}

	public function testViewCountIncrement()
	{
		$collection = factory(Collection::class)->create();

		$this->assertEquals(0, $collection->views_count);

		$collection->viewsIncrement();
		$collection->refresh();

		$this->assertEquals(1, $collection->views_count);

		$collection->viewsIncrement();
		$collection->refresh();

		$this->assertEquals(2, $collection->views_count);
	}

	public function testShowHttp()
	{
		$collection = factory(Collection::class)
			->create();

		$this->actingAs($collection->create_user)
			->get(route('collections.show', ['collection' => $collection]))
			->assertOk();

		$collection->refresh();

		$this->assertEquals(1, $collection->views_count);
	}

	public function testToggleToFavorites()
	{
		$collection = factory(Collection::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('collections.favorite.toggle', ['collection' => $collection]))
			->assertOk()
			->assertJsonFragment(['result' => 'attached', 'count' => 1]);

		$collection->refresh();
		$user->refresh();

		$this->assertEquals(1, $collection->added_to_favorites_users_count);
		$this->assertEquals(1, $user->data->favorite_collections_count);

		$this->actingAs($user)
			->get(route('collections.favorite.toggle', ['collection' => $collection]))
			->assertOk()
			->assertJsonFragment(['result' => 'detached', 'count' => 0]);

		$collection->refresh();
		$user->refresh();

		$this->assertEquals(0, $collection->added_to_favorites_users_count);
		$this->assertEquals(0, $user->data->favorite_collections_count);
	}

	public function testAddLike()
	{
		$collection = factory(Collection::class)
			->create();

		$user = factory(User::class)->states('administrator')->create();

		$response = $this->actingAs($user)
			->get(route('likes.store', ['type' => 18, 'id' => $collection->id]))
			->assertOk();

		$like = $collection->likes()->first();

		$collection->refresh();

		$response->assertJsonFragment($collection->toArray());
		$response->assertJsonFragment($like->toArray());

		$this->assertEquals(1, $collection->like_count);
	}

	public function testUserSeesScopeOnlyMe()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create()
			->fresh();

		$creator = $collection->create_user;

		$this->assertEquals(1, Collection::where('id', $collection->id)
			->userSees($creator)
			->count());

		$user = factory(User::class)->create();

		$this->assertEquals(0, Collection::where('id', $collection->id)
			->userSees($user)
			->count());
	}

	public function testUserSeesScopeEveryone()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create()
			->fresh();

		$creator = $collection->create_user;

		$this->assertEquals(1, Collection::where('id', $collection->id)
			->userSees($creator)
			->count());

		$user = factory(User::class)->create();

		$this->assertEquals(1, Collection::where('id', $collection->id)
			->userSees($user)
			->count());
	}
	/*
		public function testScopeUserSeesFriend()
		{
			$collection = factory(Collection::class)
				->create(['who_can_see' => 'friends'])
				->fresh();

			$creator = $collection->create_user;

			$this->assertEquals(1, Collection::where('id', $collection->id)
				->userSees($creator)
				->count());

			$user = factory(User::class)->create();

			$this->assertEquals(0, Collection::where('id', $collection->id)
				->userSees($user)
				->count());

			$relation = factory(UserRelation::class)
				->create([
					'user_id' => $creator->id,
					'status' => \App\Enums\UserRelationType::Friend
				]);

			$user = $relation->second_user;

			$this->assertTrue($creator->isFriendOf($user));

			$this->assertEquals(1, Collection::where('id', $collection->id)
				->userSees($user)
				->count());

			$relation = factory(UserRelation::class)
				->create([
					'user_id2' => $creator->id,
					'status' => \App\Enums\UserRelationType::Subscriber
				]);

			$user = $relation->first_user;

			$this->assertTrue($user->isSubscriberOf($creator));

			$this->assertEquals(0, Collection::where('id', $collection->id)
				->userSees($user)
				->count());
		}
		*/
	/*
		public function testScopeUserSeesSubscriberAndFriends()
		{
			$collection = factory(Collection::class)
				->create(['who_can_see' => 'friends_and_subscribers'])
				->fresh();

			$creator = $collection->create_user;

			$this->assertEquals(1, Collection::where('id', $collection->id)
				->userSees($creator)
				->count());

			$user = factory(User::class)->create();

			$this->assertEquals(0, Collection::where('id', $collection->id)
				->userSees($user)
				->count());

			$relation = factory(UserRelation::class)
				->create([
					'user_id' => $creator->id,
					'status' => \App\Enums\UserRelationType::Friend
				]);

			$user = $relation->second_user;

			$this->assertTrue($creator->isFriendOf($user));

			$this->assertEquals(1, Collection::where('id', $collection->id)
				->userSees($user)
				->count());

			$relation = factory(UserRelation::class)
				->create([
					'user_id2' => $creator->id,
					'status' => \App\Enums\UserRelationType::Subscriber
				]);

			$user = $relation->first_user;

			$this->assertTrue($user->isSubscriberOf($creator));
			$this->assertTrue($creator->isSubscriptionOf($user));

			$this->assertEquals(1, Collection::where('id', $collection->id)
				->userSees($user)
				->count());
		}
	*/


	public function testScopeSeeEveryone()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create()
			->fresh();

		$this->assertEquals(1, Collection::where('id', $collection->id)
			->seeEveryone()
			->count());

		$collection->status = StatusEnum::Private;
		$collection->save();
		$collection->refresh();

		$this->assertEquals(0, Collection::where('id', $collection->id)
			->seeEveryone()
			->count());
	}

	public function testAttachBook()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create(['who_can_add' => 'everyone']);

		$this->assertNull($collection->latest_updates_at);

		$book = factory(Book::class)
			->create();

		$user = factory(User::class)
			->states('admin')
			->create();

		$number = rand(1, 200);
		$comment = $this->faker->realText(200);

		$this->actingAs($user)
			->post(route('collections.books.attach', ['collection' => $collection]), [
				'book_id' => $book->id,
				'number' => $number,
				'comment' => $comment
			])
			->assertSessionHasNoErrors()
			->assertRedirect(route('collections.books', $collection));

		$collection->refresh();

		$book2 = $collection->books()->first();

		$this->assertEquals(1, $collection->books_count);
		$this->assertEquals($book->id, $book2->id);
		$this->assertEquals($number, $book2->collected_book->number);
		$this->assertEquals($comment, $book2->collected_book->comment);
		$this->assertEquals($user->id, $book2->collected_book->create_user_id);
		$this->assertNotNull($collection->latest_updates_at);
	}

	public function testDetachBook()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$collected_book = factory(CollectedBook::class)
			->create();

		$collection = $collected_book->collection;
		$collection->status = StatusEnum::Accepted;
		$collection->who_can_add = 'everyone';
		$collection->save();
		$collection->refresh();

		$this->assertNull($collection->latest_updates_at);

		$book = $collected_book->book;

		$this->actingAs($user)
			->get(route('collections.books.detach', ['collection' => $collection, 'book' => $book]))
			->assertRedirect(route('collections.books', $collection));

		$collection->refresh();

		$this->assertEquals(0, $collection->books_count);
		$this->assertNotNull($collection->latest_updates_at);
	}

	public function testBooksSelectHttp()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$collection = factory(Collection::class)
			->states('accepted')
			->create(['who_can_add' => 'everyone']);

		$this->actingAs($user)
			->get(route('collections.books.select', $collection))
			->assertOk();
	}

	public function testBooksHttp()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$collected_book = factory(CollectedBook::class)
			->create();

		$collection = $collected_book->collection;
		$collection->status = StatusEnum::Accepted;
		$collection->who_can_add = 'everyone';
		$collection->save();
		$collection->refresh();

		$book = $collected_book->book;

		$response = $this->actingAs($user)
			->get(route('collections.books', $collection))
			->assertOk();
		//->assertSeeText($book->title);

		$resource = $response->original->gatherData()['resource'];

		$this->assertFalse($resource->isSaveSetting());
		$this->assertEquals('gallery', $resource->getDefaultInputValue('view'));
		$this->assertEquals('gallery', $resource->getInputValue('view'));
	}

	public function testCollectedBookUpdate()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$collected_book = factory(CollectedBook::class)
			->create();

		$collection = $collected_book->collection;
		$collection->status = StatusEnum::Accepted;
		$collection->who_can_add = 'everyone';
		$collection->save();
		$collection->refresh();

		$this->assertNull($collection->latest_updates_at);

		$book = $collected_book->book;

		$number = rand(1, 100);
		$comment = $this->faker->realText(200);

		$this->actingAs($user)
			->post(route('collections.books.update', ['collection' => $collection, 'book' => $book]), [
				'number' => $number,
				'comment' => $comment,
				'book_id' => $book->id
			])
			->assertSessionHasNoErrors()
			->assertRedirect(route('collections.books.edit', ['collection' => $collection, 'book' => $book]));

		$collected_book->refresh();
		$collection->refresh();

		$this->assertEquals($number, $collected_book->number);
		$this->assertEquals($comment, $collected_book->comment);
		$this->assertNotNull($collection->latest_updates_at);
	}

	public function testCollectedBookEdit()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$collected_book = factory(CollectedBook::class)
			->create();

		$collection = $collected_book->collection;
		$collection->status = StatusEnum::Accepted;
		$collection->who_can_add = 'everyone';
		$collection->save();
		$collection->refresh();

		$book = $collected_book->book;

		$this->actingAs($user)
			->get(route('collections.books.edit', ['collection' => $collection, 'book' => $book]))
			->assertOk();
	}

	public function testUserCreatedCollectionsHttp()
	{
		$collection = factory(Collection::class)
			->create(['title' => uniqid()]);

		$user = $collection->create_user;

		$this->actingAs($user)
			->get(route('users.collections.created', ['user' => $user]))
			->assertOk()
			->assertSeeText($collection->title);
	}

	public function testUserFavoriteCollectionsHttp()
	{
		$collection = factory(Collection::class)
			->create(['title' => uniqid()]);

		$user = $collection->create_user;

		$this->actingAs($user)
			->get(route('collections.favorite.toggle', ['collection' => $collection]))
			->assertOk();

		$this->actingAs($user)
			->get(route('users.collections.favorite', ['user' => $user]))
			->assertOk()
			->assertSeeText($collection->title);
	}

	public function testShowOkIfUserGuestCanSeeEveryone()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create();

		$this->get(route('collections.show', $collection))
			->assertOk();
	}


	public function testBooksOkIfUserGuestCanSeeEveryone()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create();

		$this->get(route('collections.books', $collection))
			->assertOk();
	}

	public function testShowForbiddenIfUserGuestCanSeeMe()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create();

		$this->get(route('collections.show', $collection))
			->assertForbidden();
	}

	public function testBooksForbiddenIfUserGuestCanSeeMe()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create();

		$this->get(route('collections.books', $collection))
			->assertForbidden();
	}

	public function testShareValue()
	{
		$collected_book = factory(CollectedBook::class)
			->create();

		$collection = $collected_book->collection;
		$book = $collected_book->book;

		$this->assertEquals(__('collection.collection') . ' "' . $collection->title . '" - ' . $collection->books_count . ' ' . mb_strtolower(trans_choice('collection.books', $collection->books_count)),
			$collection->getShareTitle());

		$this->assertEquals($collection->description, $collection->getShareDescription());
		$this->assertEquals(null, $collection->getShareImage());
		$this->assertEquals(route('collections.show', $collection), $collection->getShareUrl());
		$this->assertEquals(__('collection.share_a_collection'), $collection->getShareTooltip());
	}

	public function testSearchByIsbn()
	{
		$user = factory(User::class)->states('admin')->create();

		$title = uniqid();
		$isbn = rand(100, 999) . '-' . rand(1, 9) . '-' . rand(100, 999) . '-' . rand(10000, 99999) . '-' . rand(1, 9);

		$book = factory(Book::class)
			->create(['title' => $title, 'pi_isbn' => $isbn]);

		$this->actingAs($user)
			->get(route('collections.books.list', ['query' => $isbn]))
			->assertOk()
			->assertSeeText($book->title);
	}

	public function testSearchTitle()
	{
		$title = Str::random(5);
		$description = Str::random(5);

		$collection = factory(Collection::class)
			->create([
				'title' => $title,
				'description' => $description
			]);

		$this->actingAs($collection->create_user)
			->get(route('collections.index', [
				'search' => $title
			]))
			->assertOk()
			->assertDontSeeText(__('collection.nothing_found'))
			->assertSeeText($title)
			->assertSeeText($description);
	}

	public function testIsOkIfOpenCollectionWithNoAccess()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create();

		$this->get(route('collections.books.select', $collection))
			->assertStatus(401);
	}

	public function testCollectionNotInteger()
	{
		$this->get(route('collections.show', ['collection' => Str::random(8)]))
			->assertNotFound();
	}

	public function testPerPage()
	{
		$response = $this->get(route('collections.index', ['per_page' => 5]))
			->assertOk();

		$this->assertEquals(10, $response->original->gatherData()['collections']->perPage());

		$response = $this->get(route('collections.index', ['per_page' => 200]))
			->assertOk();

		$this->assertEquals(100, $response->original->gatherData()['collections']->perPage());
	}

	public function testSeeCollectionForEveryoneInList()
	{
		$title = Str::random(8);

		$collection = factory(Collection::class)
			->states('accepted')
			->create([
				'title' => $title
			]);

		$this->get(route('collections.index', ['title' => $title]))
			->assertOk()
			->assertSeeText($title);
	}

	public function testDontSeePrivateCollectionInList()
	{
		$title = Str::random(8);

		$collection = factory(Collection::class)
			->states('private')
			->create([
				'title' => $title
			]);

		$this->get(route('collections.index', ['title' => $title]))
			->assertOk()
			->assertDontSeeText($title);
	}

	public function testCreateComplainReportHttpIsOk()
	{
		$user = factory(User::class)->states('admin')->create();

		$collection = factory(Collection::class)
			->create();

		$this->actingAs($user)
			->get(route('complains.report', ['type' => '18', 'id' => $collection->id]))
			->assertOk();
	}

	public function testStoreStatusValidationError()
	{
		$user = factory(User::class)->create();

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'who_can_add' => UserAccountPermissionValues::getRandomKey(),
			'who_can_comment' => UserAccountPermissionValues::getRandomKey(),
			'url' => $this->faker->url,
			'url_title' => $this->faker->realText(50),
		];

		$response = $this->actingAs($user)
			->post(route('collections.store', $post))
			->assertRedirect();
		var_dump(session('errors'));
		$response->assertSessionHasErrors(['status' => __('validation.required', ['attribute' => __('collection.status')])]);
	}

	public function testUpdateStatusValidationError()
	{
		$collection = factory(Collection::class)->create();

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'who_can_add' => UserAccountPermissionValues::getRandomKey(),
			'who_can_comment' => UserAccountPermissionValues::getRandomKey(),
			'url' => $this->faker->url,
			'url_title' => $this->faker->realText(50),
		];

		$response = $this->actingAs($collection->create_user)
			->patch(route('collections.update', ['collection' => $collection]), $post);
		var_dump(session('errors'));
		$response->assertRedirect()
			->assertSessionHasErrors(['status' => __('validation.required', ['attribute' => __('collection.status')])]);
	}

	public function testUpdateStatusInValidationError()
	{
		$collection = factory(Collection::class)->create();

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'status' => '345345',
			'who_can_add' => UserAccountPermissionValues::getRandomKey(),
			'who_can_comment' => UserAccountPermissionValues::getRandomKey(),
			'url' => $this->faker->url,
			'url_title' => $this->faker->realText(50),
		];

		$response = $this->actingAs($collection->create_user)
			->patch(route('collections.update', ['collection' => $collection]), $post);
		var_dump(session('errors'));
		$response->assertRedirect()
			->assertSessionHasErrors(['status' => __('validation.in', ['attribute' => __('collection.status')])]);
	}

	public function testWhoCanAddAndCommentMustEqualsMeIfStatusPrivate()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create();

		$post = [
			'title' => Str::random(8) . ' ' . $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'status' => StatusEnum::Private,
			'who_can_add' => 'everyone',
			'who_can_comment' => 'everyone',
		];

		$response = $this->actingAs($collection->create_user)
			->patch(route('collections.update', ['collection' => $collection]), $post);
		var_dump(session('errors'));
		$response->assertRedirect()
			->assertSessionHasErrors([
				'who_can_add' => __('collection.validation.equals_value_if_other_field_equals',
					[
						'attribute' => __('collection.who_can_add'),
						'value' => __('collection.who_can_add_array.me'),
						'other_attribute' => __('collection.status'),
						'other_value' => __('collection.status_array.' . StatusEnum::Private)
					]),
				'who_can_comment' => __('collection.validation.equals_value_if_other_field_equals',
					[
						'attribute' => __('collection.who_can_comment'),
						'value' => __('collection.who_can_comment_array.me'),
						'other_attribute' => __('collection.status'),
						'other_value' => __('collection.status_array.' . StatusEnum::Private)
					])
			]);
	}

	public function testYouCantSelectWhoCanAddMeValueIfOtherUserBooksInCollection()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create();

		$collectedBook = factory(CollectedBook::class)
			->create(['collection_id' => $collection->id]);

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'status' => StatusEnum::Private,
			'who_can_add' => 'me',
			'who_can_comment' => 'me'
		];

		$response = $this->actingAs($collection->create_user)
			->patch(route('collections.update', $collection), $post)
			->assertRedirect();
		var_dump(session('errors'));
		$response->assertSessionHasErrors(['who_can_add' =>
			__('collection.you_cant_select_only_me_because_there_are_books_added_by_other_users_in_the_collection',
				['value' => __('collection.who_can_add_array.me')])
		]);
	}

	public function testYouCanSelectWhoCanAddMeValueIfNoOtherUserBooksInCollection()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create();

		$collectedBook = factory(CollectedBook::class)
			->create([
				'collection_id' => $collection->id,
				'create_user_id' => $collection->create_user_id
			]);

		$post = [
			'title' => $this->faker->realText(100) . ' ' . Str::random(3),
			'description' => $this->faker->realText(100) . ' ' . Str::random(3),
			'status' => StatusEnum::Private,
			'who_can_add' => 'me',
			'who_can_comment' => 'me'
		];

		$response = $this->actingAs($collection->create_user)
			->patch(route('collections.update', $collection), $post)
			->assertRedirect()
			->assertSessionHasNoErrors();
	}

	public function testYouCanSelectWhoCanAddMeValueIfBookAttachedByCollectionUser()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create();

		$collectedBook = factory(CollectedBook::class)
			->create([
				'collection_id' => $collection->id
			]);

		$collectionUser = factory(CollectionUser::class)
			->create([
				'collection_id' => $collection->id,
				'user_id' => $collectedBook->create_user_id
			]);

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'status' => StatusEnum::Private,
			'who_can_add' => 'me',
			'who_can_comment' => 'me'
		];

		$response = $this->actingAs($collection->create_user)
			->patch(route('collections.update', $collection), $post)
			->assertRedirect()
			->assertSessionHasNoErrors();
	}

	public function testOrderIsOk()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create();

		$collectedBook = factory(CollectedBook::class)
			->create([
				'collection_id' => $collection->id,
				'number' => 2,
				'comment' => 'test'
			]);

		$response = $this->get(route('collections.books', [
			'collection' => $collection,
			'order' => 'collection_number_asc'
		]))->assertOk();

		$response = $this->get(route('collections.books', [
			'collection' => $collection,
			'order' => 'collection_number_desc'
		]))->assertOk();

		$response = $this->get(route('collections.books', [
			'collection' => $collection,
			'order' => 'oldest_added_to_collection'
		]))->assertOk();

		$response = $this->get(route('collections.books', [
			'collection' => $collection,
			'order' => 'latest_added_to_collection'
		]))->assertOk();
	}
}
