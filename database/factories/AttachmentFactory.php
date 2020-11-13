<?php

namespace Database\Factories;

use App\Attachment;
use App\Book;

class AttachmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attachment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_id' => Book::factory()->private(),
            'name' => 'test.jpg',
            'content_type' => 'image/jpeg',
            'size' => '18964',
            'type' => 'image',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Attachment $attachment) {
            $attachment->openImage(__DIR__.'/../../tests/Feature/images/test.jpeg');
        });
    }

    public function cover()
    {
        return $this->afterCreating(function (Attachment $attachment) {
            $attachment->book->cover_id = $attachment->id;
            $attachment->push();
        });
    }
}
