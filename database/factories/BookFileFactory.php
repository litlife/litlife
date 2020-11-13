<?php

namespace Database\Factories;

use App\Book;
use App\BookFile;
use App\BookFileDownloadLog;
use App\Enums\StatusEnum;
use App\User;
use Database\Factories\Traits\CheckedItems;

class BookFileFactory extends Factory
{
    use CheckedItems;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookFile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => uniqid().'.txt',
            'format' => 'txt',
            'book_id' => Book::factory(),
            'create_user_id' => User::factory(),
            'size' => rand(1245, 345346),
            'file_size' => rand(1245, 345346),
            'md5' => $this->faker->md5,
            'status' => StatusEnum::Accepted
        ];
    }

    public function txt()
    {
        return $this->afterMaking(function (BookFile $file) {
            //$file->name = $file->book->name_for_book_file;
            $tmp = tmpfile();
            fwrite($tmp, 'text text text');
            $file->open($tmp, 'txt');

            $file->format = 'txt';
        });
    }

    public function odt()
    {
        return $this->afterMaking(function (BookFile $file) {
            $string = 'text text text';

            $stream = tmpfile();
            fwrite($stream, $string);

            $file->open($stream, 'odt');
        });
    }

    public function fb2()
    {
        return $this->afterMaking(function (BookFile $file) {
            $string = 'text text text';

            $stream = tmpfile();
            fwrite($stream, $string);

            $file->open($stream, 'fb2');
        });
    }

    public function zip()
    {
        return $this->afterMaking(function (BookFile $file) {
            $file->zip = true;
        });
    }

    public function storage_public()
    {
        return $this->afterMaking(function (BookFile $file) {
            $file->storage = 'public';
        });
    }

    public function storage_private()
    {
        return $this->afterMaking(function (BookFile $file) {
            $file->storage = 'private';
        });
    }

    public function storage_old()
    {
        return $this->afterMaking(function (BookFile $file) {
            $file->storage = 'old';
        });
    }

    public function with_download_log()
    {
        return $this->afterCreating(function (BookFile $file) {
            $log = BookFileDownloadLog::factory()->create(['book_file_id' => $file->id]);

            $file->refreshDownloadCount();
            $file->save();
        });
    }
}
