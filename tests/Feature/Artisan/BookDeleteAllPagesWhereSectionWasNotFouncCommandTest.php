<?php

namespace Tests\Feature\Artisan;

use App\Section;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BookDeleteAllPagesWhereSectionWasNotFouncCommandTest extends TestCase
{
	public function testDontDeleteIfSectionWasFound()
	{
		$section = Section::factory()->with_two_pages()->create();

		$page = $section->pages()->first();

		Artisan::call('book:delete_all_pages_where_section_was_not_found', ['latest_page_id' => $page->id]);

		$this->assertDatabaseHas('pages', [
			'id' => $page->id
		]);
	}

	public function testDontDeleteIfSectionSoftDeleted()
	{
		$section = Section::factory()->with_two_pages()->create();

		$page = $section->pages()->first();

		Section::where('id', $section->id)
			->delete();

		Artisan::call('book:delete_all_pages_where_section_was_not_found', ['latest_page_id' => $page->id]);

		$this->assertDatabaseHas('pages', [
			'id' => $page->id
		]);
	}

	public function testDeleteIfSectionForceDeleted()
	{
		$section = Section::factory()->with_two_pages()->create();

		$page = $section->pages()->first();

		Section::where('id', $section->id)
			->forceDelete();

		Artisan::call('book:delete_all_pages_where_section_was_not_found', ['latest_page_id' => $page->id]);

		$this->assertDatabaseMissing('pages', [
			'id' => $page->id
		]);
	}
}
