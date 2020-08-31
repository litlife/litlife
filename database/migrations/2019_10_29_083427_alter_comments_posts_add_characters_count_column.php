<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCommentsPostsAddCharactersCountColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('posts', function (Blueprint $table) {
			$table->integer('characters_count')->nullable();
		});

		Schema::table('comments', function (Blueprint $table) {
			$table->integer('characters_count')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('posts', function (Blueprint $table) {
			$table->dropColumn('characters_count');
		});

		Schema::table('comments', function (Blueprint $table) {
			$table->dropColumn('characters_count');
		});
	}
}
