<?php

namespace Database\Factories;

use App\Book;
use App\Jobs\Book\UpdateBookNotesCount;
use App\Jobs\Book\UpdateBookPagesCount;
use App\Jobs\Book\UpdateBookSectionsCount;
use App\Page;
use App\Section;
use Database\Factories\Traits\CheckedItems;

class SectionFactory extends Factory
{
    use CheckedItems;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Section::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->realText(200),
            'type' => 'section',
            'book_id' => Book::factory()->private()->with_create_user(),
            'created_at' => now(),
            'updated_at' => now(),
            '_lft' => '1',
            '_rgt' => '2'
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            if ($item->pages_count < 1) {
                $item->pages()->save(Page::factory()->make(
                    [
                        'book_id' => $item->book_id,
                        'page' => '1',
                        'content' => '<p>'.$this->faker->realText(800).'</p>'
                    ]
                ));

                $item->pages()->save(Page::factory()->make(
                    [
                        'book_id' => $item->book_id,
                        'page' => '2',
                        'content' => '<p>'.$this->faker->realText(800).'</p>'
                    ]
                ));

                unset($item->pages);

                $item->pages_count = $item->pages()->count();
                $item->refreshCharactersCount();

                if ($item->type == 'section') {
                    UpdateBookSectionsCount::dispatch($item->book);
                }

                if ($item->type == 'note') {
                    UpdateBookNotesCount::dispatch($item->book);
                }

                UpdateBookPagesCount::dispatch($item->book);

                $item->book->refreshCharactersCount();
            }
        });
    }

    public function annotation()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {

        })->state(function (array $attributes) {
            return [
                'type' => 'annotation'
            ];
        });
    }

    public function note()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {

        })->state(function (array $attributes) {
            return [
                'type' => 'note',
            ];
        });
    }

    public function chapter()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {

        })->state(function (array $attributes) {
            return [
                'type' => 'section',
            ];
        });
    }

    public function book_private()
    {
        return $this->afterMaking(function ($item) {
            $item->book->statusPrivate();
            $item->book->save();
        })->afterCreating(function ($item) {

        });
    }

    public function no_pages()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->pages()->delete();
            $item->pages_count = $item->pages()->count();
            $item->save();

            if ($item->type == 'section') {
                UpdateBookSectionsCount::dispatch($item->book);
            }

            if ($item->type == 'note') {
                UpdateBookNotesCount::dispatch($item->book);
            }

            UpdateBookPagesCount::dispatch($item->book);

            $item->refreshCharactersCount();
            $item->book->refreshCharactersCount();
        });
    }

    public function with_three_pages()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->pages()->delete();

            $item->pages()->save(Page::factory()->make(
                [
                    'book_id' => $item->book_id,
                    'page' => 1,
                    'content' => '<p>'.$this->faker->realText(800).'</p>'
                ]
            ));

            $item->pages()->save(Page::factory()->make(
                [
                    'book_id' => $item->book_id,
                    'page' => 2,
                    'content' => '<p>'.$this->faker->realText(800).'</p>'
                ]
            ));

            $item->pages()->save(Page::factory()->make(
                [
                    'book_id' => $item->book_id,
                    'page' => 3,
                    'content' => '<p>'.$this->faker->realText(800).'</p>'
                ]
            ));

            $item->pages_count = $item->pages()->count();
            $item->save();

            if ($item->type == 'section') {
                UpdateBookSectionsCount::dispatch($item->book);
            }

            if ($item->type == 'note') {
                UpdateBookNotesCount::dispatch($item->book);
            }

            UpdateBookPagesCount::dispatch($item->book);

            $item->refreshCharactersCount();
            $item->book->refreshCharactersCount();
        });
    }

    public function with_two_pages()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->pages()->delete();

            $item->pages()->save(Page::factory()->make(
                [
                    'book_id' => $item->book_id,
                    'page' => 1,
                    'content' => '<p>'.$this->faker->realText(800).'</p>'
                ]
            ));

            $item->pages()->save(Page::factory()->make(
                [
                    'book_id' => $item->book_id,
                    'page' => 2,
                    'content' => '<p>'.$this->faker->realText(800).'</p>'
                ]
            ));

            $item->pages_count = $item->pages()->count();
            $item->save();

            if ($item->type == 'section') {
                UpdateBookSectionsCount::dispatch($item->book);
            }

            if ($item->type == 'note') {
                UpdateBookNotesCount::dispatch($item->book);
            }

            UpdateBookPagesCount::dispatch($item->book);

            $item->refreshCharactersCount();
            $item->book->refreshCharactersCount();
        });
    }
}
