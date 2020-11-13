<?php

namespace Database\Factories;

use App\Book;
use App\Enums\StatusEnum;
use App\Sequence;
use App\User;
use Database\Factories\Traits\CheckedItems;

class SequenceFactory extends Factory
{
    use CheckedItems;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sequence::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->realText(30),
            'description' => $this->faker->realText(50),
            'create_user_id' => User::factory(),
            'status' => StatusEnum::Accepted,
            'status_changed_at' => now(),
            'status_changed_user_id' => rand(50000, 100000)
        ];
    }

    public function with_book()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $book = Book::factory()->with_section()->create();

            $item->books()->detach();
            $item->books()->attach([$book->id]);
            $item->refreshBooksCount();
            $item->save();
        });
    }

    public function with_two_books()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $book = Book::factory()->with_section()->create();

            $book2 = Book::factory()->with_section()->create();

            $item->books()->detach();
            $item->books()->attach([$book->id]);
            $item->books()->attach([$book2->id]);
            $item->refreshBooksCount();
            $item->save();
        });
    }
}
