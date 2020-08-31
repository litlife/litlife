<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookAuthorsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_authors')) {
			Schema::create('book_authors', function (Blueprint $table) {
				$table->bigInteger('book_id')->index();
				$table->bigInteger('author_id')->index('ba_author_id_idx');
				$table->integer('time')->default(0);
				$table->integer('order')->nullable();
				$table->timestamps();
				$table->smallInteger('type')->default(0)->comment('Автор или переводчик или редактор и тп');
				$table->unique(['book_id', 'author_id', 'type']);
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
		Schema::drop('book_authors');
	}

}
