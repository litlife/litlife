<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('collections', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('title')->comment(__('collection.title'));
			$table->string('description')->nullable()->comment(__('collection.description'));
			$table->tinyInteger('who_can_see')->comment(__('collection.who_can_see'))->index();
			$table->tinyInteger('who_can_add')->comment(__('collection.who_can_add'));
			$table->tinyInteger('who_can_comment')->comment(__('collection.who_can_comment'));
			$table->string('lang', 2)->nullable()->comment(__('collection.lang'));
			$table->string('url', 200)->nullable()->comment(__('collection.url'));
			$table->string('url_title', 200)->nullable()->comment(__('collection.url_title'));
			$table->integer('cover_id')->nullable()->comment(__('collection.cover_id'));
			$table->integer('create_user_id')->comment(__('collection.create_user_id'))->index();
			$table->integer('books_count')->default(0)->comment(__('collection.books_count'));
			$table->integer('comments_count')->nullable()->comment(__('collection.comments_count'));
			$table->integer('added_to_favorites_users_count')->nullable()->comment(__('collection.added_to_favorites_users_count'));
			$table->integer('views_count')->nullable()->comment(__('collection.views_count'));
			$table->integer('like_count')->nullable()->comment(__('collection.like_count'));
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('collections');
	}
}
