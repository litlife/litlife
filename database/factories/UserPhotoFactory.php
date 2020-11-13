<?php

namespace Database\Factories;

use App\User;
use App\UserPhoto;
use Imagick;
use ImagickPixel;

class UserPhotoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserPhoto::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function ($item) {
            $image = new Imagick();
            $image->newImage(300, 300, new ImagickPixel('red'));
            $image->setImageFormat('jpeg');

            $item->openImage($image);
        })->afterCreating(function ($item) {

        });
    }
}
