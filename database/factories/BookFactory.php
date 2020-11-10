<?php

use App\Attachment;
use App\Author;
use App\Book;
use App\BookFile;
use App\BookKeyword;
use App\Enums\BookComplete;
use App\Enums\StatusEnum;
use App\Genre;
use App\Jobs\Book\BookGroupJob;
use App\Jobs\Book\UpdateBookAttachmentsCount;
use App\Jobs\Book\UpdateBookFilesCount;
use App\Jobs\Book\UpdateBookNotesCount;
use App\Jobs\Book\UpdateBookPagesCount;
use App\Jobs\Book\UpdateBookSectionsCount;
use App\Manager;
use App\Section;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Book::class, function (Faker $faker) {
    return [
        'title' => trim(mb_substr($faker->realText(500), 0, 100)) . ' ' . Str::random(10),
        'page_count' => 0,
        'ti_lb' => 'EN',
        'ti_olb' => 'RU',
        'pi_bn' => $faker->realText(20),
        'pi_pub' => $faker->realText(20),
        'pi_city' => $faker->city,
        'pi_year' => $faker->year,
        'pi_isbn' => $faker->isbn13,
        'create_user_id' => 0,
        'is_si' => true,
        'year_writing' => $faker->year,
        'rightholder' => $faker->realText(20),
        'year_public' => $faker->year,
        'is_public' => $faker->boolean,
        'age' => 0,
        'is_lp' => $faker->boolean,
        'status' => StatusEnum::Accepted,
        'status_changed_at' => now(),
        'status_changed_user_id' => rand(50000, 100000),
        'ready_status' => BookComplete::getRandomKey(),
        'online_read_new_format' => true
    ];
});

$factory->afterCreating(App\Book::class, function ($book, $faker) {
    /*
        $author = factory(\App\Author::class)->create([
            'status' => $book->status,
            'create_user_id' => $book->create_user_id
        ]);

        $book->writers()->sync([$author->id]);
        $book->refresh();
    */
});

$factory->afterCreatingState(App\Book::class, 'with_genre', function ($book, $faker) {

    $count = Genre::count();

    if (empty($count)) {
        $genre = factory(Genre::class)->states('with_main_genre')->create();
    } else {
        $genre = Genre::inRandomOrder()->notMain()->first();
    }

    $book->genres()->sync([$genre->id]);
    $book->push();
});

$factory->afterCreatingState(App\Book::class, 'with_create_user', function ($book, $faker) {

    $book->create_user_id = factory(App\User::class)->create()->id;
    $book->save();
    $book->refresh();
});

$factory->afterCreatingState(App\Book::class, 'with_writer', function ($book, $faker) {

    $author = factory(Author::class)->create([
        'status' => $book->status,
        'create_user_id' => $book->create_user_id
    ]);

    $book->writers()->sync([$author->id]);
});

$factory->afterCreatingState(App\Book::class, 'without_any_authors', function ($book, $faker) {

    $book->authors()->detach();
    $book->push();

    unset($book->writers);
    unset($book->authors);
});

$factory->afterCreating(App\Book::class, function ($book, $faker) {
    /*
        $author = factory(\App\Author::class)->create([
            'status' => $book->status,
            'create_user_id' => $book->create_user_id
        ]);

        $book->writers()->sync([$author->id]);
        $book->refresh();
        */
});

$factory->afterCreatingState(App\Book::class, 'with_compiler', function ($book, $faker) {

    $author = factory(Author::class)->create([
        'status' => $book->status,
        'create_user_id' => $book->create_user_id
    ]);

    $book->compilers()->sync([$author->id]);
    $book->refresh();
});

$factory->afterCreatingState(App\Book::class, 'with_translator', function ($book, $faker) {

    $author = factory(Author::class)->create([
        'status' => $book->status,
        'create_user_id' => $book->create_user_id
    ]);

    $book->translators()->sync([$author->id]);
    $book->refresh();
});

$factory->afterCreatingState(App\Book::class, 'with_illustrator', function ($book, $faker) {

    $author = factory(Author::class)->create([
        'status' => $book->status,
        'create_user_id' => $book->create_user_id
    ]);

    $book->illustrators()->sync([$author->id]);
    $book->refresh();
});

$factory->afterCreatingState(App\Book::class, 'with_editor', function ($book, $faker) {

    $author = factory(Author::class)->create([
        'status' => $book->status,
        'create_user_id' => $book->create_user_id
    ]);

    $book->editors()->sync([$author->id]);
    $book->refresh();
});

$factory->afterCreatingState(App\Book::class, 'with_cover', function ($book, $faker) {

    $attachment = factory(Attachment::class)->create([
        'book_id' => $book->id
    ]);

    $book->cover_id = $attachment->id;
    $book->save();
    $book->refresh();

    UpdateBookAttachmentsCount::dispatch($book);
});

$factory->afterCreatingState(App\Book::class, 'with_attachment', function ($book, $faker) {

    $attachment = factory(Attachment::class)->create([
        'book_id' => $book->id
    ]);

    UpdateBookAttachmentsCount::dispatch($book);
});

$factory->afterCreatingState(App\Book::class, 'with_annotation', function ($book, $faker) {

    $section = factory(Section::class)
        ->state('annotation')
        ->create(['type' => 'annotation', 'book_id' => $book->id]);

    $book->save();
    $book->refresh();
});

$factory->afterCreatingState(App\Book::class, 'with_keyword', function ($book, $faker) {

    $book_keyword = factory(BookKeyword::class)
        ->create(['book_id' => $book->id]);

    $book->save();
    $book->refresh();
});

$factory->afterCreatingState(App\Book::class, 'with_section', function (Book $book, Faker $faker) {

    $section = factory(Section::class)
        ->state('accepted')
        ->create([
            'book_id' => $book->id
        ]);

    $book->refreshCharactersCount();

    if ($section->type == 'section') {
        UpdateBookSectionsCount::dispatch($book);
    }

    if ($section->type == 'note') {
        UpdateBookNotesCount::dispatch($book);
    }

    UpdateBookPagesCount::dispatch($book);
});

$factory->afterCreatingState(App\Book::class, 'with_note', function (Book $book, Faker $faker) {

    $section = factory(Section::class)
        ->states('accepted', 'note')
        ->create([
            'book_id' => $book->id
        ]);

    $book->refreshCharactersCount();

    UpdateBookNotesCount::dispatch($book);
    UpdateBookPagesCount::dispatch($book);
});

$factory->afterCreatingState(App\Book::class, 'with_file', function (Book $book, Faker $faker) {

    $file = factory(BookFile::class)
        ->states('private', 'txt')
        ->create([
            'book_id' => $book->id,
            'create_user_id' => $book->create_user_id
        ]);

    UpdateBookFilesCount::dispatch($book);
});

$factory->afterCreatingState(App\Book::class, 'with_source', function (Book $book, Faker $faker) {

    $file = factory(BookFile::class)
        ->states('private', 'txt')
        ->create([
            'book_id' => $book->id,
            'create_user_id' => $book->create_user_id,
            'source' => true
        ]);

    UpdateBookFilesCount::dispatch($book);
});

$factory->afterCreatingState(App\Book::class, 'with_accepted_file', function (Book $book, Faker $faker) {

    $file = factory(BookFile::class)
        ->states('accepted', 'txt')
        ->create([
            'book_id' => $book->id,
            'create_user_id' => $book->create_user_id
        ]);

    UpdateBookFilesCount::dispatch($book);
});


$factory->afterCreatingState(App\Book::class, 'with_paid_section', function ($book, $faker) {

    $section = factory(Section::class)
        ->state('paid')
        ->create([
            'book_id' => $book->id
        ]);

    if ($section->type == 'section') {
        UpdateBookSectionsCount::dispatch($book);
    }

    if ($section->type == 'note') {
        UpdateBookNotesCount::dispatch($book);
    }

    UpdateBookPagesCount::dispatch($book);
});

$factory->afterMakingState(App\Book::class, 'private', function (Book $book, $faker) {
    $book->statusPrivate();
});

$factory->afterMakingState(App\Book::class, 'accepted', function (Book $book, $faker) {
    $book->statusAccepted();
});

$factory->afterMakingState(App\Book::class, 'sent_for_review', function (Book $book, $faker) {
    $book->statusSentForReview();
});

$factory->afterCreatingState(App\Book::class, 'parsed', function (Book $book, $faker) {
    $book->parse->success();
    $book->parse->save();
});


$factory->afterCreatingState(App\Book::class, 'with_three_sections', function (Book $book, $faker) {

    $section = factory(Section::class)->create([
        'book_id' => $book->id
    ]);

    $section = factory(Section::class)->create([
        'book_id' => $book->id
    ]);

    $section = factory(Section::class)->create([
        'book_id' => $book->id
    ]);

    UpdateBookSectionsCount::dispatch($book);
    UpdateBookPagesCount::dispatch($book);

    $book->refreshCharactersCount();
});

$factory->afterMakingState(App\Book::class, 'with_read_and_download_access', function (Book $book, $faker) {
    $book->readAccessEnable();
    $book->downloadAccessEnable();
});

$factory->afterMakingState(App\Book::class, 'closed', function (Book $book, $faker) {
    $book->readAccessDisable();
    $book->downloadAccessDisable();
});

$factory->afterCreatingState(App\Book::class, 'with_author_manager', function (Book $book, $faker) {

    $author = factory(Author::class)->create([
        'status' => $book->status,
        'create_user_id' => $book->create_user_id
    ]);

    $book->writers()->sync([$author->id]);
    $book->refresh();

    $author = $book->writers->first();

    $manager = factory(Manager::class)
        ->create([
            'character' => 'author'
        ]);

    $author->managers()->save($manager);
    $author->refresh();

});

$factory->afterMakingState(App\Book::class, 'complete', function (Book $book, $faker) {
    $book->ready_status = 'complete';
});

$factory->afterMakingState(App\Book::class, 'not_complete_but_still_writing', function (Book $book, $faker) {
    $book->ready_status = 'not_complete_but_still_writing';
});

$factory->afterMakingState(App\Book::class, 'removed_from_sale', function (Book $book, $faker) {
    $book->statusReject();
    $book->price = null;
});

$factory->afterMakingState(App\Book::class, 'soft_deleted', function (Book $book, $faker) {
    $book->deleted_at = now();
});

$factory->afterMakingState(App\Book::class, 'lp_false', function (Book $book, $faker) {
    $book->is_lp = false;
});

$factory->afterMakingState(App\Book::class, 'lp_true', function (Book $book, $faker) {
    $book->is_lp = true;
    $book->pi_pub = '';
    $book->pi_city = '';
    $book->pi_year = '';
    $book->pi_isbn = '';
});

$factory->state(App\Book::class, 'publish_fields_empty', function ($faker) {
    return [
        'pi_pub' => null,
        'pi_city' => null,
        'pi_year' => null,
        'pi_isbn' => null
    ];
});

$factory->state(App\Book::class, 'si_true', function ($faker) {
    return [
        'is_si' => true,
        'pi_pub' => null,
        'pi_city' => null,
        'pi_year' => null,
        'pi_isbn' => null
    ];
});

$factory->state(App\Book::class, 'si_false', function ($faker) {
    return [
        'is_si' => false
    ];
});

$factory->afterMakingState(App\Book::class, 'on_sale', function (Book $book, $faker) {
    $book->price = rand(50, 100);
});

$factory->afterCreatingState(App\Book::class, 'with_minor_book', function (Book $book, $faker) {

    $minorBook = factory(Book::class)
        ->create();

    BookGroupJob::dispatch($book, $minorBook);
});

$factory->afterCreatingState(App\Book::class, 'with_two_minor_books', function (Book $book, $faker) {

    $minorBook = factory(Book::class)
        ->create();

    BookGroupJob::dispatch($book, $minorBook);

    $minorBook = factory(Book::class)
        ->create();

    BookGroupJob::dispatch($book, $minorBook);
});

$factory->afterMakingState(App\Book::class, 'description_only', function ($faker) {
    return [
        'files_count' => 0,
        'sections_count' => 0,
        'page_count' => 0
    ];
});
