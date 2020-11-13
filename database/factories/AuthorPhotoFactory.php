<?php

namespace Database\Factories;

use App\Author;
use App\AuthorPhoto;
use App\User;

class AuthorPhotoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AuthorPhoto::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'author_id' => Author::factory(),
            'name' => uniqid().'.jpeg',
            'create_user_id' => User::factory(),
            'size' => rand(1245, 345346),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (AuthorPhoto $author_photo) {
            $author_photo->openImage(__DIR__.'/../../tests/Feature/images/test.jpeg');
            $author_photo->author->photos()->save($author_photo);
        });
    }
}
