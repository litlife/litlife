<?php

namespace Tests\Feature\Book;

use App\Author;
use App\Book;
use App\Genre;
use App\Section;
use App\Sequence;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookEditTest extends TestCase
{
	public function testEditHttp()
	{
		$user = factory(User::class)
			->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$book = factory(Book::class)
			->create();

		$this->actingAs($user)
			->get(route('books.edit', ['book' => $book]))
			->assertOk();
	}

	public function testUpdateHttpAnotherAuthorAppear()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book')
			->create();

		$user = $author->managers->first()->user;

		$book = $author->books()->first();
		$book->create_user_id = $user->id;
		$book->save();

		$this->assertTrue($book->isAccepted());
		$this->assertTrue($user->can('update', $book));

		$author = factory(Author::class)
			->create(['last_name' => 'test']);

		$post = [
			'title' => $book->title,
			'genres' => [$book->genres()->first()->id],
			'writers' => [$book->writers()->first()->id, $author->id],
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete'
		];

		$this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertTrue($book->isSentForReview());

		$book->statusAccepted();
		$book->save();
		$book->refresh();

		$post['ti_lb'] = 'EN';

		$this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertTrue($book->isAccepted());
	}

	public function testEditTitle()
	{
		$user = factory(User::class)
			->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$book = factory(Book::class)
			->states('with_writer', 'with_genre')
			->create();

		$array = $book->toArray();
		$array = [
			'title' => 'V.',
			'genres' => $book->genres()->pluck('id')->toArray(),
			'writers' => $book->writers()->any()->pluck('id')->toArray(),
			'ti_lb' => 'RU', 'ti_olb' => 'RU', 'ready_status' => 'complete'
		];

		$this->actingAs($user)
			->followingRedirects()
			->get(route('books.edit', $book))
			->assertOk();

		$response = $this->patch(route('books.update', $book), $array)
			->assertRedirect();

		$response->assertSessionHasNoErrors();

		$book->refresh();

		$this->assertEquals('V.', $book->title);

		$this->assertEquals($book->title_search_helper,
			mb_strtolower($book->title));
	}

	public function testUpdateHttp()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$book = factory(Book::class)
			->create();

		$fillable = $book->getFillable();

		$post = collect(factory(Book::class)
			->make(['is_si' => false])
			->toArray());

		$post = $post->filter(function ($value, $key) use ($fillable) {
			return in_array($key, $fillable);
		})->toArray();

		$post['genres'] = factory(Genre::class, 2)->states('age_0', 'with_main_genre')->create()->pluck('id')->toArray();
		$post['writers'] = factory(Author::class, 2)->create()->pluck('id')->toArray();
		$post['translators'] = factory(Author::class, 2)->create()->pluck('id')->toArray();
		$post['sequences'] = [
			[
				'id' => factory(Sequence::class)->create()->id,
				'number' => rand(0, 3),
				'order' => 0
			], [
				'id' => factory(Sequence::class)->create()->id,
				'number' => rand(0, 3),
				'order' => 1
			]
		];
		$post['editors'] = factory(Author::class, 2)->create()->pluck('id')->toArray();
		$post['compilers'] = factory(Author::class, 2)->create()->pluck('id')->toArray();
		$post['illustrators'] = factory(Author::class, 2)->create()->pluck('id')->toArray();
		$post['annotation'] = $this->faker->realText(100);
		$post['is_public'] = rand(0, 1) ? true : false;

		$this->actingAs($user)
			->patch(route('books.update', ['book' => $book]), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertEquals($post['title'], $book->title);
		$this->assertEquals($post['ti_lb'], $book->ti_lb);
		$this->assertEquals($post['ti_olb'], $book->ti_olb);
		$this->assertEquals($post['pi_bn'], $book->pi_bn);
		$this->assertEquals($post['pi_pub'], $book->pi_pub);
		$this->assertEquals($post['pi_city'], $book->pi_city);
		$this->assertEquals($post['pi_year'], $book->pi_year);
		$this->assertEquals($post['pi_isbn'], $book->pi_isbn);
		$this->assertEquals($post['is_si'], $book->is_si);
		$this->assertEquals($post['year_writing'], $book->year_writing);
		$this->assertEquals($post['rightholder'], $book->rightholder);
		$this->assertEquals($post['age'], $book->age);
		$this->assertEquals($post['is_lp'], $book->is_lp);
		$this->assertEquals($post['ready_status'], $book->ready_status);

		$this->assertEquals($post['genres'], $book->genres()->pluck('id')->toArray());
		$this->assertEquals($post['writers'], $book->writers()->pluck('id')->toArray());
		$this->assertEquals($post['translators'], $book->translators()->pluck('id')->toArray());

		$sequences = $book->sequences()->get();

		$this->assertEquals($post['sequences'], [
			[
				'id' => $sequences[0]->id,
				'number' => $sequences[0]->pivot->number,
				'order' => $sequences[0]->pivot->order,
			], [
				'id' => $sequences[1]->id,
				'number' => $sequences[1]->pivot->number,
				'order' => $sequences[1]->pivot->order,
			]
		]);

		$this->assertEquals($post['editors'], $book->editors()->pluck('id')->toArray());
		$this->assertEquals($post['compilers'], $book->compilers()->pluck('id')->toArray());
		$this->assertEquals($post['illustrators'], $book->illustrators()->pluck('id')->toArray());
		$this->assertEquals('<p>' . $post['annotation'] . '</p>', $book->annotation->getContent());

		//$this->assertEquals(1, $book->activities()->count());

		$activity = $book->activities()->first();

		$this->assertNotNull($activity);
		$this->assertEquals($activity->subject_id, $book->id);
		$this->assertEquals($activity->subject_type, 'book');
		$this->assertEquals($activity->causer_id, $user->id);
		$this->assertEquals($activity->causer_type, 'user');
		$this->assertEquals($activity->description, 'updated');
	}

	public function testUpdateAuthorsHttpAuthorsBooksCounters()
	{
		$admin = factory(User::class)
			->states('administrator')
			->create();

		$book = factory(Book::class)
			->states('with_genre')
			->create();

		$writer = factory(Author::class)->create();
		$translator = factory(Author::class)->create();
		$editor = factory(Author::class)->create();
		$compiler = factory(Author::class)->create();
		$illustrator = factory(Author::class)->create();

		$post = [
			'title' => $book->title,
			'genres' => [$book->genres()->first()->id],
			'writers' => [$writer->id],
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete'
		];

		$this->actingAs($admin)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$this->assertEquals(1, $writer->fresh()->books_count);
		$this->assertEquals(0, $translator->fresh()->books_count);
		$this->assertEquals(0, $editor->fresh()->books_count);
		$this->assertEquals(0, $compiler->fresh()->books_count);
		$this->assertEquals(0, $illustrator->fresh()->books_count);

		$post['translators'] = [$translator->id];

		$this->actingAs($admin)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$this->assertEquals(1, $writer->fresh()->books_count);
		$this->assertEquals(1, $translator->fresh()->books_count);
		$this->assertEquals(0, $editor->fresh()->books_count);
		$this->assertEquals(0, $compiler->fresh()->books_count);
		$this->assertEquals(0, $illustrator->fresh()->books_count);

		$post['translators'] = [];
		$post['editors'] = [$editor->id];

		$this->actingAs($admin)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$this->assertEquals(1, $writer->fresh()->books_count);
		$this->assertEquals(0, $translator->fresh()->books_count);
		$this->assertEquals(1, $editor->fresh()->books_count);
		$this->assertEquals(0, $compiler->fresh()->books_count);
		$this->assertEquals(0, $illustrator->fresh()->books_count);

		$post['editors'] = [];
		$post['compilers'] = [$compiler->id];

		$this->actingAs($admin)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$this->assertEquals(1, $writer->fresh()->books_count);
		$this->assertEquals(0, $translator->fresh()->books_count);
		$this->assertEquals(0, $editor->fresh()->books_count);
		$this->assertEquals(1, $compiler->fresh()->books_count);
		$this->assertEquals(0, $illustrator->fresh()->books_count);

		$post['compilers'] = [];
		$post['illustrators'] = [$illustrator->id];

		$this->actingAs($admin)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$this->assertEquals(1, $writer->fresh()->books_count);
		$this->assertEquals(0, $translator->fresh()->books_count);
		$this->assertEquals(0, $editor->fresh()->books_count);
		$this->assertEquals(0, $compiler->fresh()->books_count);
		$this->assertEquals(1, $illustrator->fresh()->books_count);
	}

	public function testUpdateAuthorsHttpChangedRating()
	{
		$admin = factory(User::class)
			->states('administrator')
			->create();

		$book = factory(Book::class)
			->states('with_genre')
			->create();

		$writer = factory(Author::class)->create();
		$editor = factory(Author::class)->create();

		$post = [
			'title' => $book->title,
			'genres' => [$book->genres()->first()->id],
			'writers' => [$writer->id],
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete'
		];

		$this->assertFalse($writer->isRatingChanged());
		$this->assertFalse($editor->isRatingChanged());

		$this->actingAs($admin)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$this->assertTrue($writer->fresh()->isRatingChanged());
		$this->assertFalse($editor->fresh()->isRatingChanged());

		$post['editors'] = [$editor->id];

		$this->actingAs($admin)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$this->assertTrue($writer->fresh()->isRatingChanged());
		$this->assertTrue($editor->fresh()->isRatingChanged());
	}

	public function testCopyProtection()
	{
		$book = factory(Book::class)
			->states('with_writer', 'with_genre', 'with_create_user', 'private')
			->create();

		$user = $book->create_user;

		$array = [
			'title' => $book->title,
			'genres' => $book->genres()->pluck('id')->toArray(),
			'writers' => $book->writers()->any()->pluck('id')->toArray(),
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'copy_protection' => false
		];

		$response = $this->actingAs($user)
			->patch(route('books.update', $book), $array);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect(route('books.edit', $book));

		$this->assertFalse($book->fresh()->copy_protection);

		$array['copy_protection'] = true;

		$response = $this->actingAs($user)
			->patch(route('books.update', $book), $array);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect(route('books.edit', $book));

		$this->assertTrue($book->fresh()->copy_protection);
	}


	public function testSeeEditFieldOfPublicDomain()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$book = factory(Book::class)
			->create();

		$this->actingAs($user)
			->get(route('books.edit', $book))
			->assertOk()
			->assertSeeText(__('book.is_public'))
			->assertSeeText(__('book.year_public'))
			->assertSeeText(__('book.is_public_helper'));

		$user->group->edit_field_of_public_domain = false;
		$user->push();

		$this->actingAs($user)
			->get(route('books.edit', $book))
			->assertOk()
			->assertDontSeeText(__('book.is_public'))
			->assertDontSeeText(__('book.year_public'))
			->assertDontSeeText(__('book.is_public_helper'));
	}

	public function testCantChangeIfEditFieldOfPublicDomainEnabled()
	{
		$user = factory(User::class)
			->states('admin')
			->create();
		$user->group->edit_field_of_public_domain = false;
		$user->push();

		$book = factory(Book::class)->states('with_writer', 'with_genre')->create();
		$book->is_public = true;
		$book->year_public = 2005;
		$book->push();

		$post = [
			'title' => $book->title,
			'genres' => $book->genres()->pluck('id')->toArray(),
			'writers' => $book->writers()->any()->pluck('id')->toArray(),
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'is_public' => false,
			'year_public' => 1995
		];

		$response = $this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.edit', $book));

		$book->refresh();

		$this->assertEquals(true, $book->is_public);
		$this->assertEquals(2005, $book->year_public);
	}

	public function testCanChangeIfEditFieldOfPublicDomainEnabled()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$book = factory(Book::class)
			->states('with_writer', 'with_genre')
			->create();

		$post = [
			'title' => $book->title,
			'genres' => $book->genres()->pluck('id')->toArray(),
			'writers' => $book->writers()->any()->pluck('id')->toArray(),
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'is_public' => true,
			'year_public' => rand(2000, 2010)
		];

		$response = $this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.edit', $book));

		$book->refresh();

		$this->assertEquals($post['is_public'], $book->is_public);
		$this->assertEquals($post['year_public'], $book->year_public);
	}

	public function testAnnotationLengthIsMoreThanAllowed()
	{
		config(['litlife.max_section_characters_count' => '10']);

		$admin = factory(User::class)
			->states('administrator')
			->create();

		$book = factory(Book::class)
			->states('with_genre')
			->create();

		$writer = factory(Author::class)
			->create();

		$annotation = Str::random(11);

		$post = [
			'title' => $book->title,
			'genres' => [$book->genres()->first()->id],
			'writers' => [$writer->id],
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'annotation' => $annotation
		];

		$this->actingAs($admin)
			->patch(route('books.update', $book), $post)
			->assertRedirect()
			->assertSessionHasErrors(['content' => __('validation.max.string', [
				'max' => config('litlife.max_annotation_characters_count'),
				'attribute' => __('book.annotation')
			])]);
	}

	public function testAnnotationLengthIsLessThanAllowed()
	{
		config(['litlife.max_section_characters_count' => '10']);

		$admin = factory(User::class)
			->states('administrator')
			->create();

		$book = factory(Book::class)
			->states('with_genre')
			->create();

		$writer = factory(Author::class)
			->create();

		$annotation = '<p>' . Str::random(5) . '      ' . Str::random(5) . '</p>';

		$post = [
			'title' => $book->title,
			'genres' => [$book->genres()->first()->id],
			'writers' => [$writer->id],
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'annotation' => $annotation
		];

		$this->actingAs($admin)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();
	}

	public function testCantEditIfUserCreatorAndEditSelfBookPermissionDisable()
	{
		$book = factory(Book::class)
			->states('accepted', 'with_create_user')
			->create();

		$user = $book->create_user;
		$user->group->edit_self_book = false;
		$user->push();

		$this->assertFalse($user->can('update', $book));
	}

	public function testCanEditIfUserCreatorAndEditSelfBookPermissionEnable()
	{
		$book = factory(Book::class)
			->states('accepted', 'with_create_user')
			->create();

		$user = $book->create_user;
		$user->group->edit_self_book = true;
		$user->push();

		$this->assertTrue($user->can('update', $book));
	}

	public function testMakeABookAnAmateurTranslationIfTheBookIsSIAndATranslatorIsAdded()
	{
		$admin = factory(User::class)
			->states('administrator')
			->create();

		$book = factory(Book::class)
			->states('with_genre')
			->create();

		$writer = factory(Author::class)
			->create();

		$translator = factory(Author::class)
			->create();

		$post = [
			'title' => $book->title,
			'genres' => [$book->genres()->first()->id],
			'writers' => [$writer->id],
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'is_si' => '1',
			'is_lp' => '0',
			'translators' => [$translator->id],
		];

		$this->actingAs($admin)
			->patch(route('books.update', $book), $post)
			->assertRedirect(route('books.edit', $book))
			->assertSessionHasErrors(['is_si' => __('book.you_cant_set_the_si_label_if_the_translator_is_specified')]);
	}

	public function testCantCutAnnotationIfBookForSale()
	{
		config(['litlife.min_annotation_characters_count_for_sale' => 10]);

		$author = factory(Author::class)
			->states('with_book_for_sale', 'with_author_manager_can_sell')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;

		$book->is_si = true;
		$book->create_user()->associate($user);
		$book->save();

		$annotation = factory(Section::class)
			->states('annotation')
			->create(['book_id' => $book->id]);

		$this->assertNotNull($book->fresh()->annotation);
		$this->assertTrue($book->isForSale());

		$input = [
			'title' => $book->title,
			'genres' => [$book->genres()->first()->id],
			'writers' => $book->writers()->any()->pluck('id')->toArray(),
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'annotation' => '123'
		];

		$response = $this->actingAs($user)
			->patch(route('books.update', ['book' => $book]), $input)
			->assertRedirect()
			->assertSessionHasErrors(['annotation' => __('book.annotation_must_contain_at_least_characters_for_sale', [
				'characters_count' => config('litlife.min_annotation_characters_count_for_sale')
			])]);

		$this->assertEquals('123', session('_old_input')['annotation']);

		$input['annotation'] = '12345678910';

		$response = $this->actingAs($user)
			->patch(route('books.update', ['book' => $book]), $input)
			->assertRedirect()
			->assertSessionHasNoErrors();
	}

}
