<?php

namespace Tests\Feature\TextBlock;

use App\Enums\TextBlockShowEnum;
use App\TextBlock;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class TextBlockTest extends TestCase
{
	public function testCreateHttp()
	{
		$admin = factory(User::class)->states('admin')->create();

		$name = uniqid();

		$this->actingAs($admin)
			->get(route('text_blocks.create', ['name' => $name]))
			->assertOk();
	}

	public function testStoreHttp()
	{
		$admin = factory(User::class)->states('admin')->create();

		$name = uniqid();
		$text = $this->faker->realText(100);

		$response = $this->actingAs($admin)
			->post(route('text_blocks.store', ['name' => $name]),
				[
					'text' => $text,
					'show_for_all' => TextBlockShowEnum::Administration
				])
			->assertSessionHasNoErrors();

		$textBlock = TextBlock::where('name', $name)->first();

		$this->assertNotNull($textBlock);
		$this->assertEquals($name, $textBlock->name);
		$this->assertEquals($text, $textBlock->text);
		$this->assertNotNull($textBlock->user_edited_at);
		$this->assertEquals(TextBlockShowEnum::Administration, $textBlock->show_for_all);
		$this->assertEquals($admin->id, $textBlock->user_id);

		$response->assertRedirect(route('text_blocks.show', ['name' => $textBlock->name, 'id' => $textBlock->id]));
	}

	public function testShowHttp()
	{
		$textBlock = factory(TextBlock::class)
			->states('show_for_admin')
			->create();

		$this->get(route('text_blocks.show', ['name' => $textBlock->name, 'id' => $textBlock->id]))
			->assertOk()
			->assertDontSeeText(strip_tags($textBlock->text));

		$textBlock = factory(TextBlock::class)
			->states('show_for_all')
			->create();

		$this->get(route('text_blocks.show', ['name' => $textBlock->name, 'id' => $textBlock->id]))
			->assertOk()
			->assertSeeText(strip_tags($textBlock->text));
	}

	public function testEditHttp()
	{
		$admin = factory(User::class)->states('admin')->create();

		$textBlock = factory(TextBlock::class)->create();

		$this->actingAs($admin)
			->get(route('text_blocks.edit', ['name' => $textBlock->name]))
			->assertOk()
			->assertSeeText($textBlock->text);
	}

	public function testUpdateHttp()
	{
		$admin = factory(User::class)->states('admin')->create();

		$textBlock = factory(TextBlock::class)->create();

		$text = $this->faker->realText(100);

		Carbon::setTestNow(now()->addDay());

		$this->actingAs($admin)
			->patch(route('text_blocks.update', ['name' => $textBlock->name]),
				[
					'text' => $text,
					'show_for_all' => TextBlockShowEnum::All
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$textBlockNew = TextBlock::latestVersion($textBlock->name);

		$this->assertNotNull($textBlockNew);
		$this->assertEquals($textBlock->name, $textBlockNew->name);
		$this->assertEquals($text, $textBlockNew->text);
		$this->assertNotNull($textBlockNew->user_edited_at);
		$this->assertEquals(TextBlockShowEnum::All, $textBlockNew->show_for_all);
		$this->assertEquals($admin->id, $textBlockNew->user_id);
	}

	public function testShowLatestVersionHttp()
	{
		$name = uniqid();

		$textBlock1 = factory(TextBlock::class)->create(['name' => $name, 'created_at' => Carbon::now()->addDays(1), 'show_for_all' => TextBlockShowEnum::All]);
		$textBlock2 = factory(TextBlock::class)->create(['name' => $name, 'created_at' => Carbon::now()->addDays(3), 'show_for_all' => TextBlockShowEnum::All]);
		$textBlock3 = factory(TextBlock::class)->create(['name' => $name, 'created_at' => Carbon::now()->addDays(2), 'show_for_all' => TextBlockShowEnum::All]);

		$this->get(route('text_blocks.show_lastest_version_for_name', ['name' => $name]))
			->assertOk()
			->assertSeeText(strip_tags($textBlock2->text));
	}

	public function testCreatePolicy()
	{
		$user = factory(User::class)->create();

		$this->assertFalse($user->can('create', TextBlock::class));

		$user->group->text_block = true;
		$user->push();
		$user->refresh();

		$this->assertTrue($user->can('create', TextBlock::class));
	}

	public function testUpdatePolicy()
	{
		$user = factory(User::class)->create();
		$textBlock = factory(TextBlock::class)->create();

		$this->assertFalse($user->can('update', $textBlock));

		$user->group->text_block = true;
		$user->push();
		$user->refresh();

		$this->assertTrue($user->can('update', $textBlock));
	}

	public function testDeletePolicy()
	{
		$user = factory(User::class)->create();
		$textBlock = factory(TextBlock::class)->create();

		$this->assertFalse($user->can('delete', $textBlock));

		$user->group->text_block = true;
		$user->push();
		$user->refresh();

		$this->assertTrue($user->can('delete', $textBlock));
	}

	public function testCanViewIfNotAdminAndShowForAll()
	{
		$admin = factory(User::class)->create();

		$textBlock = factory(TextBlock::class)
			->states('show_for_all')
			->create();

		$this->assertTrue($admin->can('view', $textBlock));
	}

	public function testCantViewIfNotAdminAndShowForAdmin()
	{
		$admin = factory(User::class)->create();

		$textBlock = factory(TextBlock::class)
			->states('show_for_admin')
			->create();

		$this->assertFalse($admin->can('view', $textBlock));
	}

	public function testCanViewIfAdminAndShowForAdmin()
	{
		$admin = factory(User::class)->create();
		$admin->group->text_block = true;
		$admin->push();

		$textBlock = factory(TextBlock::class)
			->states('show_for_admin')
			->create();

		$this->assertTrue($admin->can('view', $textBlock));
	}

	public function testWelcomeHttp()
	{
		$this->get(route('welcome'))
			->assertOk();
	}

	public function testKeywordsHelperHttp()
	{
		$this->get(route('text_block.keywords_helper'))
			->assertOk();
	}

	public function testPaidBookPublishingRulesHttp()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user)
			->get(route('paid_book_publishing_rules'))
			->assertOk();
	}

	public function testSalesRulesHttp()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user)
			->get(route('sales_rules'))
			->assertOk();
	}

	public function testPurchaseRulesHttp()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user)
			->get(route('purchase_rules'))
			->assertOk();
	}

	public function testPersonalDataProcessingAgreementHttp()
	{
		$this->get(route('personal_data_processing_agreement'))
			->assertOk();
	}

	public function testRulesHttp()
	{
		$this->get(route('rules'))
			->assertOk();
	}

	public function testForRightOwnersHttp()
	{
		$this->get(route('for_rights_owners'))
			->assertOk();
	}

	public function testRulesPublishBooksHttp()
	{
		$this->get(route('rules_publish_books'))
			->assertOk();
	}

	public function testShowVersionNotFound()
	{
		$this->get(route('text_blocks.show', ['name' => Str::random(8), 'id' => rand(1, 10)]))
			->assertNotFound();
	}

	public function testVersionsIndexIsOk()
	{
		$user = factory(User::class)->states('admin')->create();

		$textBlock = factory(TextBlock::class)->create();

		$this->actingAs($user)
			->get(route('text_blocks.versions.index', ['name' => $textBlock->name]))
			->assertOk();
	}

	public function testVersionsIndexNotFound()
	{
		$user = factory(User::class)->states('admin')->create();

		$this->actingAs($user)
			->get(route('text_blocks.versions.index', ['name' => Str::random(8)]))
			->assertNotFound();
	}

	public function testShowVersion()
	{
		$textBlock = factory(TextBlock::class)
			->states('show_for_all')
			->create(['text' => Str::random(10)]);

		$textBlock2 = factory(TextBlock::class)
			->states('show_for_all')
			->create(['name' => $textBlock->name, 'text' => Str::random(10)]);

		$this->get(route('text_blocks.show', ['name' => $textBlock->name, 'id' => $textBlock->id]))
			->assertOk()
			->assertSeeText($textBlock->text)
			->assertDontSeeText($textBlock2->text);

		$this->get(route('text_blocks.show', ['name' => $textBlock2->name, 'id' => $textBlock2->id]))
			->assertOk()
			->assertSeeText($textBlock2->text)
			->assertDontSeeText($textBlock->text);
	}
}
