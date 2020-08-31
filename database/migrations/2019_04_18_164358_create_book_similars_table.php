<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookSimilarsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_similars')) {
			Schema::create('book_similars', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->bigInteger('book_id')->index('bsi_book_id_idx');
				$table->bigInteger('book_id2')->index('bsi_book_id2_idx');
				$table->integer('rating')->default(0)->index('bsi_rating_idx');
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
		Schema::drop('book_similars');
	}

}
