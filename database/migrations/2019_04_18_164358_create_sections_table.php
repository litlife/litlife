<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSectionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('sections')) {
			Schema::create('sections', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->integer('inner_id');
				$table->string('type')->default('section');
				$table->bigInteger('book_id');
				$table->text('title');
				$table->timestamps();
				$table->softDeletes();
				$table->integer('_lft')->default(0);
				$table->integer('_rgt')->default(0);
				$table->integer('parent_id')->nullable();
				$table->integer('character_count')->nullable();
				$table->dateTime('user_edited_at')->nullable();
				$table->text('parameters')->nullable();
				$table->text('html_tags_ids')->nullable()->comment('Массив всех id html тегов, которые содержатся в тексте');
				$table->smallInteger('pages_count')->default(0);
				$table->index(['_lft', '_rgt', 'parent_id']);
				$table->unique(['created_at', 'id']);
				$table->index(['type', 'book_id']);
				$table->index(['inner_id', 'type', 'book_id', 'deleted_at']);
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
		Schema::drop('sections');
	}

}
