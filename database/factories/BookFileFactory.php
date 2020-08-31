<?php

use App\BookFile;
use App\BookFileDownloadLog;
use App\Enums\StatusEnum;
use Faker\Generator as Faker;

$factory->define(App\BookFile::class, function (Faker $faker) {
	return [
		'name' => uniqid() . '.txt',
		'format' => 'txt',
		'book_id' => function () {
			return factory(App\Book::class)->create()->id;
		},
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'size' => rand(1245, 345346),
		'file_size' => rand(1245, 345346),
		'md5' => $faker->md5,
		'status' => StatusEnum::Accepted
	];
});

$factory->afterMaking(App\BookFile::class, function (BookFile $file, $faker) {

});

$factory->afterMakingState(App\BookFile::class, 'private', function (BookFile $file, $faker) {
	$file->statusPrivate();
});

$factory->afterMakingState(App\BookFile::class, 'accepted', function (BookFile $file, $faker) {
	$file->statusAccepted();
});

$factory->afterMakingState(App\BookFile::class, 'sent_for_review', function (BookFile $file, $faker) {
	$file->statusSentForReview();
});

$factory->afterMakingState(App\BookFile::class, 'txt', function (BookFile $file, $faker) {

	//$file->name = $file->book->name_for_book_file;
	$tmp = tmpfile();
	fwrite($tmp, 'text text text');
	$file->open($tmp, 'txt');

	$file->format = 'txt';
});

$factory->afterMakingState(App\BookFile::class, 'odt', function (BookFile $file, $faker) {

	$string = 'text text text';

	$stream = tmpfile();
	fwrite($stream, $string);

	$file->open($stream, 'odt');
});

$factory->afterMakingState(App\BookFile::class, 'fb2', function (BookFile $file, $faker) {

	$string = 'text text text';

	$stream = tmpfile();
	fwrite($stream, $string);

	$file->open($stream, 'fb2');
});

$factory->afterMakingState(App\BookFile::class, 'zip', function (BookFile $file, $faker) {

	$file->zip = true;
});

$factory->afterMakingState(App\BookFile::class, 'storage_public', function (BookFile $file, $faker) {
	$file->storage = 'public';
});

$factory->afterMakingState(App\BookFile::class, 'storage_private', function (BookFile $file, $faker) {
	$file->storage = 'private';
});

$factory->afterMakingState(App\BookFile::class, 'storage_old', function (BookFile $file, $faker) {
	$file->storage = 'old';
});

$factory->afterCreatingState(App\BookFile::class, 'with_download_log', function (BookFile $file, $faker) {

	$log = factory(BookFileDownloadLog::class)
		->create(['book_file_id' => $file->id]);

	$file->refreshDownloadCount();
	$file->save();
});


