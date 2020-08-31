<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersFavoriteCollectionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_favorite_collections', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('collection_id')->comment(__('users_favorite_collections.collection_id'));
			$table->integer('user_id')->comment(__('users_favorite_collections.user_id'));
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
		Schema::dropIfExists('user_favorite_collections');
	}
}
