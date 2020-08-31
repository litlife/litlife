<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MergeGenresAndGenreGroups extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('genres', function (Blueprint $table) {
			$table->renameColumn('genre_group_id', 'old_genre_group_id');
		});

		Schema::table('genres', function (Blueprint $table) {
			$table->smallInteger('old_genre_group_id')->nullable()->comment(__('genre.old_genre_group_id'))->change();
		});

		Schema::table('genres', function (Blueprint $table) {
			$table->integer('genre_group_id')->nullable()->comment(__('genre.old_genre_group_id'));
			$table->string('fb_code', 50)->nullable()->change();
		});

		\Illuminate\Support\Facades\DB::statement('alter table genres drop constraint if exists genres_name_unique;');

		foreach (\Illuminate\Support\Facades\DB::table('genres_groups')->get() as $group) {

			$id = \Illuminate\Support\Facades\DB::table('genres')
				->insertGetId([
					'name' => $group->name,
					'book_count' => $group->book_count,
					'created_at' => $group->created_at,
					'updated_at' => $group->updated_at
				]);

			\Illuminate\Support\Facades\DB::table('genres')
				->where('old_genre_group_id', $group->id)
				->update(['genre_group_id' => $id]);
		}

		Schema::rename('genres_groups', 'old_genres_groups');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('genres', function (Blueprint $table) {
			$table->dropColumn('genre_group_id');
		});

		Schema::table('genres', function (Blueprint $table) {
			$table->renameColumn('old_genre_group_id', 'genre_group_id');
		});

		Schema::rename('old_genres_groups', 'genres_groups');
	}
}
