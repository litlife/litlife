<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookTranslatorsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_translators')) {
			Schema::create('book_translators', function (Blueprint $table) {
				$table->bigInteger('book_id');
				$table->bigInteger('translator_id');
				$table->integer('time')->default(0);
				$table->integer('order')->nullable();
				$table->timestamps();
				$table->unique(['book_id', 'translator_id']);
				$table->index(['book_id', 'translator_id'], 'book_id_translator_id_idx');
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
		Schema::drop('book_translators');
	}

}
