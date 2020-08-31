<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterGenresTablesAddSlug extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('genres', function (Blueprint $table) {
			$table->string('slug', 100)->nullable()->comment(__('genre.slug'));
		});

		Schema::table('genres_groups', function (Blueprint $table) {
			$table->string('slug', 100)->nullable()->comment(__('genre.slug'));
		});

		\Illuminate\Support\Facades\Artisan::call('refresh:genre_slugs');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('genres', function (Blueprint $table) {
			$table->dropColumn('slug');
		});

		Schema::table('genres_groups', function (Blueprint $table) {
			$table->dropColumn('slug');
		});
	}
}
