<?php

namespace Database\Factories;

use App\Author;
use App\AuthorBiography;
use App\AuthorPhoto;
use App\Book;
use App\BookVote;
use App\Comment;
use App\Enums\Gender;
use App\Enums\StatusEnum;
use App\Jobs\Author\UpdateAuthorBooksCount;
use App\Jobs\Author\UpdateAuthorCommentsCount;
use App\Jobs\Author\UpdateAuthorRating;
use App\Language;
use App\Manager;
use App\User;
use App\UserPurchase;
use Database\Factories\Traits\CheckedItems;

class AuthorFactory extends Factory
{
    use CheckedItems;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Author::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nickname' => preg_replace('/(\'|\"|ё)/iu', '', $this->faker->userName),
            'last_name' => preg_replace('/(\'|\"|ё)/iu', '', $this->faker->lastName),
            'first_name' => preg_replace('/(\'|\"|ё)/iu', '', $this->faker->firstName),
            'middle_name' => preg_replace('/(\'|\"|ё)/iu', '', $this->faker->text(rand(5, 10))),
            'lang' => function () {
                return Language::inRandomOrder()->first()->code;
            },
            'home_page' => $this->faker->url,
            'email' => $this->faker->unique()->safeEmail,
            'create_user_id' => User::factory(),
            'wikipedia_url' => 'http://ru.wikipedia.org/wiki/'.$this->faker->text(rand(5, 20)),
            'born_date' => $this->faker->date('d M Y', '-20 years'),
            'born_place' => $this->faker->city,
            'dead_date' => $this->faker->date('d M Y'),
            'dead_place' => $this->faker->city,
            'years_creation' => $this->faker->date('Y').'-'.$this->faker->date('Y'),
            'orig_last_name' => $this->faker->lastName,
            'orig_first_name' => $this->faker->firstName,
            'orig_middle_name' => $this->faker->lastName,
            'gender' => Gender::getRandomKey(),
            'status' => StatusEnum::Accepted,
            'status_changed_at' => now(),
            'status_changed_user_id' => rand(50000, 100000)
        ];
    }

    public function with_biography()
    {
        return $this->afterCreating(function (Author $author) {
            $biography = AuthorBiography::factory()->create(['author_id' => $author->id]);
        });
    }

    public function with_book()
    {
        return $this->afterCreating(function (Author $author) {
            $book = Book::factory()->with_section()->complete()->with_genre()->create();

            $book->writers()->detach();
            $book->writers()->attach([$author->id]);

            $author->refresh();
        });
    }

    public function with_si_book()
    {
        return $this->afterCreating(function (Author $author) {
            $book = Book::factory()
                ->with_section()
                ->si_true()
                ->publish_fields_empty()
                ->complete()
                ->with_genre()
                ->create();

            $book->writers()->detach();
            $book->writers()->attach([$author->id]);

            $author->refresh();
        });
    }

    public function with_complete_book()
    {
        return $this->afterCreating(function (Author $author) {
            $book = Book::factory()->with_section()->complete()->create();

            $book->writers()->detach();
            $book->writers()->attach([$author->id]);

            $author->refresh();
        });
    }

    public function with_book_cover_annotation()
    {
        return $this->afterCreating(function (Author $author) {
            $book = Book::factory()->with_cover()->with_section()->with_annotation()->complete()->with_genre()->create();

            $book->writers()->detach();
            $book->writers()->attach([$author->id]);

            $author->refresh();
        });
    }

    public function with_private_book()
    {
        return $this->afterCreating(function (Author $author) {
            $book = Book::factory()->with_section()->private()->with_genre()->create();

            $book->writers()->detach();
            $book->writers()->attach([$author->id]);
            $author->refresh();
        });
    }

    public function with_paid_section()
    {
        return $this->afterCreating(function (Author $author) {
            $book = Book::factory()->with_paid_section()->create();

            $book->writers()->syncWithoutDetaching([$author->id]);
            $author->refresh();
        });
    }

    public function with_book_for_sale()
    {
        return $this->afterCreating(function (Author $author) {
            $book = Book::factory()
                ->with_section()->without_any_authors()->complete()->with_cover()->with_annotation()->with_genre()
                ->create([
                    'price' => rand(50, 100),
                    'is_si' => true,
                    'is_lp' => false,
                    'pi_pub' => null,
                    'pi_city' => null,
                    'pi_year' => null,
                    'pi_isbn' => null
                ]);

            $book->characters_count = config('litlife.minimum_characters_count_before_book_can_be_sold') + 1000;
            $book->save();

            $book->writers()->syncWithoutDetaching([$author->id]);

            unset($book->writers);
        });
    }

    public function with_book_for_sale_purchased()
    {
        return $this->afterCreating(function (Author $author) {
            $purchase = UserPurchase::factory()->book()->create();

            $book = $purchase->purchasable;
            $book->is_si = true;
            $book->is_lp = false;
            $book->price = rand(50, 100);
            $book->ready_status = 'complete';
            $book->save();

            $book->writers()->syncWithoutDetaching([$author->id]);
            $author->refresh();

            $purchase->buyer->purchasedBookCountRefresh();
            $book->boughtTimesCountRefresh();
        });
    }

    public function with_book_removed_from_sale()
    {
        return $this->afterCreating(function (Author $author) {
            $book = Book::factory()->with_section()->without_any_authors()->removed_from_sale()->create(['price' => rand(50, 100)]);

            $book->writers()->syncWithoutDetaching([$author->id]);
            $author->refresh();
        });
    }

    public function with_book_vote()
    {
        return $this->afterCreating(function (Author $author) {
            $book = Book::factory()->create();

            $book->writers()->syncWithoutDetaching([$author->id]);

            $book_vote = BookVote::factory()->create(['book_id' => $book->id]);

            UpdateAuthorRating::dispatch($author);
            UpdateAuthorBooksCount::dispatch($author);
        });
    }

    public function with_book_comment()
    {
        return $this->afterCreating(function (Author $author) {
            $book = Book::factory()->create();

            $book->writers()->syncWithoutDetaching([$author->id]);

            $book_vote = Comment::factory()->create(['commentable_type' => 'book', 'commentable_id' => $book->id]);

            UpdateAuthorCommentsCount::dispatch($author);
        });
    }

    public function with_translated_book()
    {
        return $this->afterCreating(function (Author $author) {
            $book = Book::factory()->create();

            $author->translated_books()->syncWithoutDetaching([$book->id]);
            $author->refresh();
        });
    }

    public function with_illustrated_book()
    {
        return $this->afterCreating(function (Author $author) {
            $book = Book::factory()->create();

            $author->illustrated_books()->syncWithoutDetaching([$book->id]);
            $author->refresh();
        });
    }

    public function with_edited_book()
    {
        return $this->afterCreating(function (Author $author) {
            $book = Book::factory()->create();

            $author->edited_books()->syncWithoutDetaching([$book->id]);
            $author->refresh();
        });
    }

    public function with_compiled_book()
    {
        return $this->afterCreating(function (Author $author) {
            $book = Book::factory()->create();

            $author->compiled_books()->syncWithoutDetaching([$book->id]);
            $author->refresh();
        });
    }

    public function with_author_manager()
    {
        return $this->afterCreating(function (Author $author) {
            $manager = Manager::factory()->create([
                'character' => 'author'
            ]);

            $author->managers()->save($manager);
            $author->refresh();
        });
    }

    public function with_author_manager_sent_for_review()
    {
        return $this->afterCreating(function (Author $author) {
            $manager = Manager::factory()
                ->character_author()
                ->sent_for_review()
                ->create([
                    'character' => 'author'
                ]);

            $author->managers()->save($manager);
            $author->refresh();
        });
    }

    public function with_author_manager_can_sell()
    {
        return $this->afterCreating(function (Author $author) {
            $manager = Manager::factory()
                ->create([
                    'character' => 'author',
                    'can_sale' => true
                ]);

            $author->managers()->save($manager);
            $author->refresh();
        });
    }

    public function with_editor_manager()
    {
        return $this->afterCreating(function (Author $author) {
            $manager = Manager::factory()->character_editor()->create();

            $author->managers()->save($manager);
            $author->refresh();
        });
    }

    public function with_seller_refered()
    {
        return $this->afterCreating(function (Author $author) {
            $referer = User::factory()->create();

            $seller = $author->seller();
            $seller->referred_by_user()->associate($referer);
            $seller->save();
        });
    }

    public function with_two_managers_and_one_can_sell()
    {
        return $this->afterCreating(function (Author $author) {
            $manager = Manager::factory()->create([
                'character' => 'author',
                'can_sale' => true
            ]);

            $author->managers()->save($manager);

            $manager = Manager::factory()->create([
                'character' => 'author',
                'can_sale' => false
            ]);

            $author->managers()->save($manager);

            $author->refresh();
        });
    }

    public function with_photo()
    {
        return $this->afterCreating(function (Author $author) {

            $photo = AuthorPhoto::factory()->create([
                'author_id' => $author->id
            ]);

            $author->photo_id = $photo->id;
            $author->save();
        });
    }
}
