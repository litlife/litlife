<?php

namespace Database\Factories;

use App\Book;
use App\Collection;
use App\Comment;
use App\User;
use Database\Factories\Traits\CheckedItems;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class CommentFactory extends Factory
{
    use CheckedItems;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $text = $this->faker->realText(150).' '.Str::random(10);

        return [
            'commentable_id' => Book::factory(),
            'commentable_type' => 'book',
            'create_user_id' => User::factory(),
            'text' => $text,
            'bb_text' => $text,
            'ip' => $this->faker->ipv4,
        ];
    }

    public function collection()
    {
        return $this->afterMaking(function (Comment $comment) {
            $map = Relation::morphMap();

            $key = array_search('App\Collection', $map);

            $comment->commentable_type = $key;

            $collection = Collection::factory()->create();

            $comment->commentable_id = $collection->id;
        })->afterCreating(function (Comment $comment) {
            //
        });
    }

    public function book()
    {
        return $this->afterMaking(function (Comment $comment) {
            $comment->commentable_type = 'book';
        })->afterCreating(function (Comment $comment) {
            //
        });
    }

    public function accepted()
    {
        return $this->afterMaking(function (Comment $comment) {

        })->afterCreating(function (Comment $comment) {
            $comment->statusAccepted();
            $comment->save();
        });
    }

    public function sent_for_review()
    {
        return $this->afterMaking(function (Comment $comment) {

        })->afterCreating(function (Comment $comment) {
            $comment->statusSentForReview();
            $comment->save();
        });
    }

    public function private()
    {
        return $this->afterMaking(function (Comment $comment) {

        })->afterCreating(function (Comment $comment) {
            $comment->statusPrivate();
            $comment->save();
        });
    }
}
