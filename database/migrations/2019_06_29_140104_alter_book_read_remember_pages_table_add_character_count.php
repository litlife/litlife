<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBookReadRememberPagesTableAddCharacterCount extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('book_read_remember_pages', function (Blueprint $table) {
			$table->integer('characters_count')->nullable()->comment('Количество символов в тексте книги на момент последнего прочтения');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('book_read_remember_pages', function (Blueprint $table) {
			$table->dropColumn('characters_count');
		});
	}
}
