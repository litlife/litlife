<?php

namespace Database\Factories;

use App\Image;
use App\User;
use Imagick;
use ImagickPixel;

class ImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Image::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'storage' => config('filesystems.default'),
            'create_user_id' => User::factory()
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Image $image) {
            $imagick = new Imagick();
            $imagick->newImage(100, 100, new ImagickPixel('red'));
            $imagick->setImageFormat('png');

            $image->openImage($imagick);
        });
    }
}
