<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectedBooksTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('collected_books', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('collection_id')->comment(__('collected_books.collection_id'));
			$table->integer('book_id')->comment(__('collected_books.book_id'));
			$table->integer('create_user_id')->comment(__('collected_books.create_user_id'));
			$table->smallInteger('number')->nullable()->comment(__('collected_books.number'));
			$table->text('comment')->nullable()->comment(__('collected_books.comment'));
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('collected_books');
	}
}
