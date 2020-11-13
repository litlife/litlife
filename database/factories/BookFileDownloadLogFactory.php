<?php

namespace Database\Factories;

use App\BookFile;
use App\BookFileDownloadLog;
use App\User;

class BookFileDownloadLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookFileDownloadLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_file_id' => BookFile::factory()->txt(),
            'user_id' => User::factory(),
            'ip' => $this->faker->ipv4
        ];
    }
}
