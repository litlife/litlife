<?php

namespace Database\Factories;

use App\Smile;

class SmileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Smile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => uniqid().'.jpg',
            'simple_form' => ':'.uniqid().':',
            'description' => uniqid(),
            'for' => null
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Smile $smile) {

            $imagick = new \Imagick();
            $imagick->setFormat("jpeg");
            $imagick->newImage(20, 20, new \ImagickPixel('red'));

            $smile->openImage($imagick);
        });
    }

    public function for_new_year()
    {
        return $this->afterMaking(function ($item) {
            $item->for = 'NewYear';
        })->afterCreating(function ($item) {

        });
    }
}
