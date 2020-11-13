<?php

namespace Database\Factories;

use App\Book;
use App\BookParse;
use App\User;

class BookParseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookParse::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_id' => Book::factory(),
            'create_user_id' => User::factory()
        ];
    }

    public function waited()
    {
        return $this->afterMaking(function (BookParse $book_parse) {
            //
        });
    }

    public function reseted()
    {
        return $this->afterMaking(function (BookParse $book_parse) {
            $book_parse->reset();
        });
    }

    public function started()
    {
        return $this->afterMaking(function (BookParse $book_parse) {
            $book_parse->start();
        });
    }

    public function successed()
    {
        return $this->afterMaking(function (BookParse $book_parse) {
            $book_parse->success();
        });
    }

    public function failed()
    {
        return $this->afterMaking(function (BookParse $book_parse) {
            $error = [
                'message' => 'Message',
                'code' => '1',
                'file' => '/file.php',
                'line' => '2',
                'traceAsString' => ''
            ];

            $book_parse->failed($error);
        });
    }

    public function only_pages()
    {
        return $this->afterMaking(function (BookParse $book_parse) {
            $book_parse->parseOnlyPages();
        });
    }
}
