<?php

namespace Database\Factories;

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
use App\User;
use Database\Factories\Traits\CheckedItems;
use Illuminate\Support\Str;

class BookFactory extends Factory
{
    use CheckedItems;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = trim(mb_substr($this->faker->realText(500), 0, 100)).' '.Str::random(10);

        return [
            'title' => $title,
            'page_count' => 0,
            'ti_lb' => 'EN',
            'ti_olb' => 'RU',
            'pi_bn' => $this->faker->realText(20),
            'pi_pub' => $this->faker->realText(20),
            'pi_city' => $this->faker->city,
            'pi_year' => $this->faker->year,
            'pi_isbn' => $this->faker->isbn13,
            'create_user_id' => User::factory(),
            'is_si' => true,
            'year_writing' => $this->faker->year,
            'rightholder' => $this->faker->realText(20),
            'year_public' => $this->faker->year,
            'is_public' => $this->faker->boolean,
            'age' => 0,
            'is_lp' => $this->faker->boolean,
            'status' => StatusEnum::Accepted,
            'status_changed_at' => now(),
            'status_changed_user_id' => rand(50000, 100000),
            'ready_status' => BookComplete::getRandomKey(),
            'online_read_new_format' => true,
            'title_search_helper' => $title,
            'title_author_search_helper' => $title,
        ];
    }

    public function with_genre()
    {
        return $this->afterCreating(function (Book $book) {
            $count = Genre::count();

            if (empty($count)) {
                $genre = Genre::factory()->with_main_genre()->create();
            } else {
                $genre = Genre::inRandomOrder()->notMain()->first();
            }

            $book->genres()->sync([$genre->id]);
            $book->push();
        });
    }

    public function with_writer()
    {
        /*
        return $this->afterCreating(function (Book $book) {
            $author = Author::factory()->create([
                'status' => $book->status,
                'create_user_id' => $book->create_user_id
            ]);

            $book->writers()->sync([$author->id]);
        });
*/

        return $this
            ->with_create_user()
            ->has(
                Author::factory()
                    ->for($this->definition()['create_user_id'], 'create_user')
                    ->state(function (array $attributes, Book $book) {
                        return [
                            'status' => $book->status,
                            'create_user_id' => $book->create_user_id
                        ];
                    })
            )->afterCreating(function (Book $book) {
                $book->updateTitleAuthorsHelper();
                $book->save();
            });
    }

    public function with_create_user()
    {
        return $this->has(User::factory(), 'create_user');
    }

    public function without_any_authors()
    {
        return $this->afterCreating(function (Book $book) {
            $book->authors()->detach();
            $book->push();

            unset($book->writers);
            unset($book->authors);
        });
    }

    public function with_compiler()
    {
        return $this->afterCreating(function (Book $book) {
            $author = Author::factory()->create([
                'status' => $book->status,
                'create_user_id' => $book->create_user_id
            ]);

            $book->compilers()->sync([$author->id]);
            $book->refresh();
        });
    }

    public function with_translator()
    {
        return $this->afterCreating(function (Book $book) {
            $author = Author::factory()->create([
                'status' => $book->status,
                'create_user_id' => $book->create_user_id
            ]);

            $book->translators()->sync([$author->id]);
            $book->refresh();
        });
    }

    public function with_illustrator()
    {
        return $this->afterCreating(function (Book $book) {
            $author = Author::factory()->create([
                'status' => $book->status,
                'create_user_id' => $book->create_user_id
            ]);

            $book->illustrators()->sync([$author->id]);
            $book->refresh();
        });
    }

    public function with_editor()
    {
        return $this->afterCreating(function (Book $book) {
            $author = Author::factory()->create([
                'status' => $book->status,
                'create_user_id' => $book->create_user_id
            ]);

            $book->editors()->sync([$author->id]);
            $book->refresh();
        });
    }

    public function with_cover()
    {
        return $this->afterCreating(function (Book $book) {
            $attachment = Attachment::factory()->create([
                'book_id' => $book->id
            ]);

            $book->cover_id = $attachment->id;
            $book->save();
            $book->refresh();

            UpdateBookAttachmentsCount::dispatch($book);
        });
    }

    public function with_attachment()
    {
        return $this->afterCreating(function (Book $book) {
            $attachment = Attachment::factory()->create([
                'book_id' => $book->id
            ]);

            UpdateBookAttachmentsCount::dispatch($book);
        });
    }

    public function with_annotation()
    {
        return $this->afterCreating(function (Book $book) {
            $section = Section::factory()
                ->annotation()
                ->create(['type' => 'annotation', 'book_id' => $book->id]);

            $book->save();
            $book->refresh();
        });
    }

    public function with_keyword()
    {
        return $this->afterCreating(function (Book $book) {
            $book_keyword = BookKeyword::factory()->create(['book_id' => $book->id]);

            $book->save();
            $book->refresh();
        });
    }

    public function with_section()
    {
        return $this->afterCreating(function (Book $book) {
            $section = Section::factory()
                ->accepted()
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
    }

    public function with_note()
    {
        return $this->afterCreating(function (Book $book) {
            $section = Section::factory()
                ->accepted()
                ->note()
                ->create([
                    'book_id' => $book->id
                ]);

            $book->refreshCharactersCount();

            UpdateBookNotesCount::dispatch($book);
            UpdateBookPagesCount::dispatch($book);
        });
    }

    public function with_file()
    {
        return $this->afterCreating(function (Book $book) {
            $file = BookFile::factory()
                ->private()
                ->txt()
                ->create([
                    'book_id' => $book->id,
                    'create_user_id' => $book->create_user_id
                ]);

            UpdateBookFilesCount::dispatch($book);
        });
    }

    public function with_source()
    {
        return $this->afterCreating(function (Book $book) {
            $file = BookFile::factory()
                ->private()
                ->txt()
                ->create([
                    'book_id' => $book->id,
                    'create_user_id' => $book->create_user_id,
                    'source' => true
                ]);

            UpdateBookFilesCount::dispatch($book);
        });
    }

    public function with_accepted_file()
    {
        return $this->afterCreating(function (Book $book) {
            $file = BookFile::factory()
                ->accepted()
                ->txt()
                ->create([
                    'book_id' => $book->id,
                    'create_user_id' => $book->create_user_id
                ]);

            UpdateBookFilesCount::dispatch($book);
        });
    }

    public function with_paid_section()
    {
        return $this->afterMaking(function (Book $book) {
            $section = Section::factory()
                ->paid()
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
    }

    public function parsed()
    {
        return $this->afterCreating(function (Book $book) {
            $book->parse->success();
            $book->parse->save();
        });
    }

    public function with_three_sections()
    {
        return $this->afterCreating(function (Book $book) {
            $section = Section::factory()->create([
                'book_id' => $book->id
            ]);

            $section = Section::factory()->create([
                'book_id' => $book->id
            ]);

            $section = Section::factory()->create([
                'book_id' => $book->id
            ]);

            UpdateBookSectionsCount::dispatch($book);
            UpdateBookPagesCount::dispatch($book);

            $book->refreshCharactersCount();
        });
    }

    public function with_read_and_download_access()
    {
        return $this->afterMaking(function (Book $book) {
            $book->readAccessEnable();
            $book->downloadAccessEnable();
        });
    }

    public function closed()
    {
        return $this->afterMaking(function (Book $book) {
            $book->readAccessDisable();
            $book->downloadAccessDisable();
        });
    }

    public function with_author_manager()
    {
        return $this->afterCreating(function (Book $book) {
            $author = Author::factory()->create([
                'status' => $book->status,
                'create_user_id' => $book->create_user_id
            ]);

            $book->writers()->sync([$author->id]);
            $book->refresh();

            $author = $book->writers->first();

            $manager = Manager::factory()->create([
                'character' => 'author'
            ]);

            $author->managers()->save($manager);
            $author->refresh();
        });
    }

    public function complete()
    {
        return $this->afterMaking(function (Book $book) {
            $book->ready_status = 'complete';
        });
    }

    public function not_complete_but_still_writing()
    {
        return $this->afterMaking(function (Book $book) {
            $book->ready_status = 'not_complete_but_still_writing';
        });
    }

    public function removed_from_sale()
    {
        return $this->afterMaking(function (Book $book) {
            $book->statusReject();
            $book->price = null;
        });
    }

    public function soft_deleted()
    {
        return $this->afterMaking(function (Book $book) {
            $book->deleted_at = now();
        });
    }

    public function lp_false()
    {
        return $this->afterMaking(function (Book $book) {
            $book->is_lp = false;
        });
    }

    public function lp_true()
    {
        return $this->afterMaking(function (Book $book) {
            $book->is_lp = true;
            $book->pi_pub = '';
            $book->pi_city = '';
            $book->pi_year = '';
            $book->pi_isbn = '';
        });
    }

    public function publish_fields_empty()
    {
        return $this->state(function (array $attributes) {
            return [
                'pi_pub' => null,
                'pi_city' => null,
                'pi_year' => null,
                'pi_isbn' => null
            ];
        });
    }

    public function si_true()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_si' => true,
                'pi_pub' => null,
                'pi_city' => null,
                'pi_year' => null,
                'pi_isbn' => null
            ];
        });
    }

    public function si_false()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_si' => false
            ];
        });
    }

    public function on_sale()
    {
        return $this->state(function (array $attributes) {
            return [
                'price' => rand(50, 100)
            ];
        });
    }

    public function with_minor_book()
    {
        return $this->afterCreating(function (Book $book) {
            $minorBook = Book::factory()->create();

            BookGroupJob::dispatch($book, $minorBook);
        });
    }

    public function with_two_minor_books()
    {
        return $this->afterCreating(function (Book $book) {
            $minorBook = Book::factory()->create();

            BookGroupJob::dispatch($book, $minorBook);

            $minorBook = Book::factory()->create();

            BookGroupJob::dispatch($book, $minorBook);
        });
    }

    public function description_only()
    {
        return $this->afterCreating(function (Book $book) {
            return [
                'files_count' => 0,
                'sections_count' => 0,
                'page_count' => 0
            ];
        });
    }

    public function withReadAccess()
    {
        return $this->afterMaking(function (Book $book) {
            $book->readAccessEnable();
        });
    }

    public function withDownloadAccess()
    {
        return $this->afterMaking(function (Book $book) {
            $book->downloadAccessEnable();
        });
    }

    public function withoutReadAccess()
    {
        return $this->afterMaking(function (Book $book) {
            $book->readAccessDisable();
        });
    }

    public function withoutDownloadAccess()
    {
        return $this->afterMaking(function (Book $book) {
            $book->downloadAccessDisable();
        });
    }
}
