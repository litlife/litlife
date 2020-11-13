<?php

namespace Database\Factories;

use App\Book;
use App\BookGroup;
use App\User;

class BookGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'create_user_id' => User::factory()
        ];
    }

    public function with_one_book()
    {
        return $this->afterCreating(function (BookGroup $group) {
            $book = Book::factory()->create();
            $book->addToGroup($group);
            $book->save();

            $group->refreshBooksCount();
            $group->save();
        });
    }

    public function add_two_books()
    {
        return $this->afterCreating(function (BookGroup $group) {
            $book = Book::factory()->create();
            $book->addToGroup($group);
            $book->save();

            $book2 = Book::factory()->create();
            $book2->addToGroup($group);
            $book2->save();

            $group->refreshBooksCount();
            $group->save();
        });
    }

    public function with_main_book()
    {
        return $this->afterCreating(function (BookGroup $group) {
            $book = Book::factory()->create();
            $book->addToGroup($group, true);
            $book->save();

            $group->refreshBooksCount();
            $group->save();
        });
    }
}
