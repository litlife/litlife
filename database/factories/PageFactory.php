<?php

namespace Database\Factories;

use App\Page;

class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $content = '<p>'.$this->faker->realText(200).'</p>';

        return [
            'content' => $content,
            'page' => rand(1, 100),
            'section_id' => rand(1, 100),
            'book_id' => rand(1, 100),
            'character_count' => mb_strlen($content)
        ];
    }
}
