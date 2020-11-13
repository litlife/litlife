<?php

namespace Database\Factories;

use App\Book;
use App\BookKeyword;
use App\Enums\StatusEnum;
use App\Keyword;
use App\User;
use Database\Factories\Traits\CheckedItems;

class BookKeywordFactory extends Factory
{
    use CheckedItems;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookKeyword::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_id' => Book::factory(),
            'keyword_id' => Keyword::factory(),
            'create_user_id' => User::factory(),
            'rating' => '0',
            'created_at' => now(),
            'updated_at' => now(),
            'status' => StatusEnum::Accepted
        ];
    }

    public function private()
    {
        return $this->afterCreating(function (BookKeyword $book_keyword) {
            $book_keyword->keyword->statusPrivate();
            $book_keyword->statusPrivate();
            $book_keyword->push();
        });
    }

    public function sent_for_review()
    {
        return $this->afterCreating(function (BookKeyword $book_keyword) {
            $book_keyword->keyword->statusSentForReview();
            $book_keyword->statusSentForReview();
            $book_keyword->push();
        });
    }

    public function accepted()
    {
        return $this->afterCreating(function (BookKeyword $book_keyword) {
            $book_keyword->keyword->statusAccepted();
            $book_keyword->statusAccepted();
            $book_keyword->push();
        });
    }
}
