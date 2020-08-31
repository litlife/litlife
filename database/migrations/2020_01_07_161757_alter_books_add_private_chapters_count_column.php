<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBooksAddPrivateChaptersCountColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('books', function (Blueprint $table) {
			$table->smallInteger('private_chapters_count')->nullable()->comment(__('book.private_chapters_count'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('books', function (Blueprint $table) {
			$table->dropColumn('private_chapters_count');
		});
	}
}
