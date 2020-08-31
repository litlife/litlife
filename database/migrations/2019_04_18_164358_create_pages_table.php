<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePagesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('pages')) {
			Schema::create('pages', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->integer('section_id')->index();
				$table->text('content');
				$table->smallInteger('page');
				$table->jsonb('html_tags_ids')->nullable()->index('pages_html_tags_ids_gin_idx')->comment('Массив всех id html тегов, которые содержатся в тексте');
				$table->integer('book_id')->index();
				$table->integer('character_count')->nullable();
			});
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pages');
	}

}
