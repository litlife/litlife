<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserDatasAddFavoriteCollectionsCount extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_datas', function (Blueprint $table) {
			$table->smallInteger('favorite_collections_count')->nullable()->comment(__('user_data.favorite_collections_count'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_datas', function (Blueprint $table) {
			$table->dropColumn('favorite_collections_count');
		});
	}
}
