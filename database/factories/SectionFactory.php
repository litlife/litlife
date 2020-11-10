<?php

use App\Jobs\Book\UpdateBookNotesCount;
use App\Jobs\Book\UpdateBookPagesCount;
use App\Jobs\Book\UpdateBookSectionsCount;
use App\Section;
use Faker\Generator as Faker;

$factory->define(App\Section::class, function (Faker $faker) {

    return [
        'title' => $faker->realText(200),
        'type' => 'section',
        'book_id' => function () {
            return factory(App\Book::class)
                ->states('private', 'with_create_user')
                ->create()
                ->id;
        },
        'created_at' => now(),
        'updated_at' => now(),
        '_lft' => '1',
        '_rgt' => '2'
    ];
});

$factory->afterCreating(App\Section::class, function (Section $section, $faker) {

    if ($section->pages_count < 1) {
        $section->pages()->save(factory(App\Page::class)->make(
            [
                'book_id' => $section->book_id,
                'page' => '1',
                'content' => '<p>' . $faker->realText(800) . '</p>'
            ]
        ));

        $section->pages()->save(factory(App\Page::class)->make(
            [
                'book_id' => $section->book_id,
                'page' => '2',
                'content' => '<p>' . $faker->realText(800) . '</p>'
            ]
        ));

        unset($section->pages);

        $section->pages_count = $section->pages()->count();
        $section->refreshCharactersCount();

        if ($section->type == 'section') {
            UpdateBookSectionsCount::dispatch($section->book);
        }

        if ($section->type == 'note') {
            UpdateBookNotesCount::dispatch($section->book);
        }

        UpdateBookPagesCount::dispatch($section->book);

        $section->book->refreshCharactersCount();
    }
});

$factory->state(App\Section::class, 'annotation', function ($faker) {
    return [
        'type' => 'annotation',
    ];
});

$factory->state(App\Section::class, 'note', function ($faker) {
    return [
        'type' => 'note',
    ];
});

$factory->state(App\Section::class, 'chapter', function ($faker) {
    return [
        'type' => 'section',
    ];
});

$factory->afterMakingState(App\Section::class, 'accepted', function (Section $section, $faker) {
    $section->statusAccepted();
});

$factory->afterMakingState(App\Section::class, 'private', function (Section $section, $faker) {
    $section->statusPrivate();
});

$factory->afterMakingState(App\Section::class, 'book_private', function (Section $section, $faker) {
    $section->book->statusPrivate();
    $section->book->save();
});

$factory->afterCreatingState(App\Section::class, 'no_pages', function (Section $section, $faker) {
    $section->pages()->delete();
    $section->pages_count = $section->pages()->count();
    $section->save();

    if ($section->type == 'section') {
        UpdateBookSectionsCount::dispatch($section->book);
    }

    if ($section->type == 'note') {
        UpdateBookNotesCount::dispatch($section->book);
    }

    UpdateBookPagesCount::dispatch($section->book);

    $section->refreshCharactersCount();
    $section->book->refreshCharactersCount();
});

$factory->afterCreatingState(App\Section::class, 'with_three_pages', function (Section $section, $faker) {
    $section->pages()->delete();

    $section->pages()->save(factory(App\Page::class)->make(
        [
            'book_id' => $section->book_id,
            'page' => 1,
            'content' => '<p>' . $faker->realText(800) . '</p>'
        ]
    ));

    $section->pages()->save(factory(App\Page::class)->make(
        [
            'book_id' => $section->book_id,
            'page' => 2,
            'content' => '<p>' . $faker->realText(800) . '</p>'
        ]
    ));

    $section->pages()->save(factory(App\Page::class)->make(
        [
            'book_id' => $section->book_id,
            'page' => 3,
            'content' => '<p>' . $faker->realText(800) . '</p>'
        ]
    ));

    $section->pages_count = $section->pages()->count();
    $section->save();

    if ($section->type == 'section') {
        UpdateBookSectionsCount::dispatch($section->book);
    }

    if ($section->type == 'note') {
        UpdateBookNotesCount::dispatch($section->book);
    }

    UpdateBookPagesCount::dispatch($section->book);

    $section->refreshCharactersCount();
    $section->book->refreshCharactersCount();
});

$factory->afterCreatingState(App\Section::class, 'with_two_pages', function (Section $section, $faker) {
    $section->pages()->delete();

    $section->pages()->save(factory(App\Page::class)->make(
        [
            'book_id' => $section->book_id,
            'page' => 1,
            'content' => '<p>' . $faker->realText(800) . '</p>'
        ]
    ));

    $section->pages()->save(factory(App\Page::class)->make(
        [
            'book_id' => $section->book_id,
            'page' => 2,
            'content' => '<p>' . $faker->realText(800) . '</p>'
        ]
    ));

    $section->pages_count = $section->pages()->count();
    $section->save();

    if ($section->type == 'section') {
        UpdateBookSectionsCount::dispatch($section->book);
    }

    if ($section->type == 'note') {
        UpdateBookNotesCount::dispatch($section->book);
    }

    UpdateBookPagesCount::dispatch($section->book);

    $section->refreshCharactersCount();
    $section->book->refreshCharactersCount();
});


