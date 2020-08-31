<?php

namespace Tests\Feature\Book;

use App\Book;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookTypeAheadFilesTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testIsDataStored()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class, 6)
			->create([
				'pi_city' => 'Город',
				'rightholder' => 'Правобладатель',
				'pi_pub' => 'Издательство',
			]);

		Artisan::call('book:make_typeahead_files');

		$this->assertTrue(Storage::exists('typeahead/book/publishers.json'));
		$this->assertTrue(Storage::exists('typeahead/book/cities.json'));
		$this->assertTrue(Storage::exists('typeahead/book/rightholders.json'));

		$array = json_decode(Storage::get('typeahead/book/publishers.json'));

		$item = $array[0];

		$this->assertNotNull($item->value);
		$this->assertFalse(property_exists($item, 'count'));
		$this->assertFalse(property_exists($item, 'is_private'));
		$this->assertFalse(property_exists($item, 'is_rejected'));
		$this->assertFalse(property_exists($item, 'is_sent_for_review'));
		$this->assertFalse(property_exists($item, 'is_accepted'));
		$this->assertFalse(property_exists($item, 'is_review_starts'));

		$array = json_decode(Storage::get('typeahead/book/cities.json'));

		$item = $array[0];

		$this->assertNotNull($item->value);
		$this->assertFalse(property_exists($item, 'count'));
		$this->assertFalse(property_exists($item, 'is_private'));
		$this->assertFalse(property_exists($item, 'is_rejected'));
		$this->assertFalse(property_exists($item, 'is_sent_for_review'));
		$this->assertFalse(property_exists($item, 'is_accepted'));
		$this->assertFalse(property_exists($item, 'is_review_starts'));

		$array = json_decode(Storage::get('typeahead/book/rightholders.json'));

		$item = $array[0];

		$this->assertNotNull($item->value);
		$this->assertFalse(property_exists($item, 'count'));
		$this->assertFalse(property_exists($item, 'is_private'));
		$this->assertFalse(property_exists($item, 'is_rejected'));
		$this->assertFalse(property_exists($item, 'is_sent_for_review'));
		$this->assertFalse(property_exists($item, 'is_accepted'));
		$this->assertFalse(property_exists($item, 'is_review_starts'));
	}
}
